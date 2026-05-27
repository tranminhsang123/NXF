<?php

namespace App\Services;

use App\Models\ContentVersion;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\PronunciationAudio;
use App\Support\PublishStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminContentStudioService
{
    private const VOCAB_KEYS = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];

    public function __construct(
        private AdvancedQuizService $advancedQuizService,
        private ContentPublishQualityService $qualityService,
        private PronunciationService $pronunciationService
    ) {}

    public function lessons(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = MinnaLesson::query()
            ->with(['sections' => fn ($query) => $query->orderBy('order_index')])
            ->withCount('sections')
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('number', 'like', '%'.$search.'%');
            });
        }

        $status = (string) ($filters['status'] ?? '');
        if (in_array($status, array_keys(PublishStatus::labels()), true)) {
            $query->where('publish_status', $status);
        }

        $quality = (string) ($filters['quality'] ?? '');
        if ($quality === 'missing_audio' || $quality === 'missing_quiz') {
            $ids = MinnaLesson::query()
                ->with('sections')
                ->get()
                ->filter(function (MinnaLesson $lesson) use ($quality) {
                    $diagnostics = $this->diagnostics($lesson);

                    return $quality === 'missing_audio'
                        ? $diagnostics['missing_audio_count'] > 0
                        : $diagnostics['missing_quiz'];
                })
                ->pluck('id');

            $query->whereIn('id', $ids->all() ?: [0]);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function overview(): array
    {
        $lessons = MinnaLesson::query()->with('sections')->get();
        $diagnostics = $lessons->map(fn (MinnaLesson $lesson) => $this->diagnostics($lesson));

        return [
            'total_lessons' => $lessons->count(),
            'draft_lessons' => $lessons->where('publish_status', PublishStatus::DRAFT)->count(),
            'ready_lessons' => $diagnostics->where('qa_passed', true)->count(),
            'missing_audio_lessons' => $diagnostics->filter(fn (array $row) => $row['missing_audio_count'] > 0)->count(),
            'missing_quiz_lessons' => $diagnostics->filter(fn (array $row) => $row['missing_quiz'])->count(),
        ];
    }

    public function diagnostics(MinnaLesson $lesson): array
    {
        $lesson->loadMissing('sections');

        $items = $this->extractStudyItems($lesson);
        $audioTexts = $items
            ->pluck('japanese')
            ->map(fn (string $text) => $this->pronunciationService->normalizeText($text))
            ->filter()
            ->unique()
            ->values();
        $missingAudio = $this->missingAudioTexts($audioTexts->all());
        $miniQuiz = $this->buildMiniQuiz($items);
        $advancedQuiz = $this->advancedQuizService->build($lesson);
        $flashcards = $this->buildFlashcards($lesson);
        $quizSection = $lesson->sections->firstWhere('key', 'quiz');
        $flashcardSection = $lesson->sections->firstWhere('key', 'flashcards');
        $qualityChecklist = $this->qualityService->checklist($lesson);
        $uniqueMeanings = $items->pluck('meaning')->unique()->count();

        return [
            'lesson_id' => $lesson->id,
            'vocab_count' => $items->count(),
            'unique_meanings' => $uniqueMeanings,
            'audio_required' => $audioTexts->count(),
            'missing_audio_count' => count($missingAudio),
            'missing_audio_samples' => array_slice($missingAudio, 0, 5),
            'mini_quiz_count' => count($miniQuiz),
            'advanced_quiz_count' => count($advancedQuiz),
            'missing_quiz' => $items->count() < 4 || $uniqueMeanings < 4 || count($advancedQuiz) < 3,
            'flashcard_count' => count($flashcards),
            'generated_quiz_count' => count($quizSection?->content['mini_quiz'] ?? []) + count($quizSection?->content['advanced_quiz'] ?? []),
            'generated_flashcard_count' => count($flashcardSection?->content['cards'] ?? []),
            'qa_passed' => (bool) ($qualityChecklist['passed'] ?? false),
            'qa_blocking_count' => (int) ($qualityChecklist['blocking_count'] ?? 0),
        ];
    }

    public function createFromTemplate(array $data): MinnaLesson
    {
        return DB::transaction(function () use ($data) {
            $number = (int) ($data['number'] ?? 0);
            if ($number < 1) {
                $number = $this->nextLessonNumber();
            }

            if (MinnaLesson::query()->where('number', $number)->exists()) {
                throw ValidationException::withMessages([
                    'number' => 'Số bài này đã tồn tại.',
                ]);
            }

            $lesson = MinnaLesson::query()->create([
                'number' => $number,
                'title' => trim((string) $data['title']),
                'description' => trim((string) ($data['description'] ?? '')),
                'publish_status' => PublishStatus::DRAFT,
                'published_at' => null,
                'archived_at' => null,
            ]);

            $this->createTemplateSections($lesson);

            return $lesson->fresh('sections');
        });
    }

    public function importFile(UploadedFile $file, ?int $defaultLessonNumber = null): array
    {
        $rows = $this->normalizeRows($this->rowsFromFile($file));
        if ($rows === []) {
            throw ValidationException::withMessages([
                'file' => 'File chưa có dòng dữ liệu hợp lệ.',
            ]);
        }

        return DB::transaction(function () use ($rows, $defaultLessonNumber) {
            $lessonIds = [];
            $sectionIds = [];
            $importedItems = 0;
            $skippedRows = 0;

            foreach ($rows as $rowNumber => $row) {
                $lessonNumber = (int) ($this->firstValue($row, ['lesson_number', 'number', 'bai']) ?: ($defaultLessonNumber ?? 0));
                if ($lessonNumber < 1) {
                    $skippedRows++;
                    continue;
                }

                $lesson = MinnaLesson::query()->firstOrCreate(
                    ['number' => $lessonNumber],
                    [
                        'title' => $this->firstValue($row, ['lesson_title', 'title', 'ten_bai']) ?: 'Bài '.$lessonNumber,
                        'description' => $this->firstValue($row, ['description', 'mo_ta']) ?: 'Nhập từ Content Studio',
                        'publish_status' => PublishStatus::DRAFT,
                    ]
                );

                if ($lesson->wasRecentlyCreated) {
                    $this->createTemplateSections($lesson);
                } elseif (! $lesson->title && $title = $this->firstValue($row, ['lesson_title', 'title', 'ten_bai'])) {
                    $lesson->forceFill(['title' => $title])->save();
                }

                $sectionKey = $this->normalizeSectionKey($this->firstValue($row, ['section_key', 'section', 'phan']) ?: 'tu-vung');
                $section = $lesson->sections()->where('key', $sectionKey)->first();
                if (! $section) {
                    $section = $this->createSection($lesson, $sectionKey, $this->sectionTitle($sectionKey));
                }

                [$content, $added] = $this->mergeRowIntoSectionContent($section->content ?? [], $sectionKey, $row);
                if ($added === 0) {
                    $skippedRows++;
                    continue;
                }

                $section->forceFill(['content' => $content])->save();
                $lessonIds[$lesson->id] = $lesson->id;
                $sectionIds[$section->id] = $section->id;
                $importedItems += $added;
            }

            if ($importedItems === 0) {
                throw ValidationException::withMessages([
                    'file' => 'Không nhập được mục nào. Hãy kiểm tra cột lesson_number, jp/tu_vung và nghia/meaning.',
                ]);
            }

            return [
                'lesson_count' => count($lessonIds),
                'section_count' => count($sectionIds),
                'item_count' => $importedItems,
                'skipped_rows' => $skippedRows,
            ];
        });
    }

    public function generateQuiz(MinnaLesson $lesson): array
    {
        $lesson->loadMissing('sections');
        $items = $this->extractStudyItems($lesson);
        $miniQuiz = $this->buildMiniQuiz($items);
        $advancedQuiz = $this->advancedQuizService->build($lesson);

        if (count($miniQuiz) < 4 || count($advancedQuiz) < 3) {
            throw ValidationException::withMessages([
                'lesson' => 'Cần ít nhất 4 từ vựng/4 nghĩa khác nhau và đủ dữ liệu để tạo 3 câu quiz nâng cao.',
            ]);
        }

        $section = $this->upsertGeneratedSection($lesson, 'quiz', 'Quiz tự động', [
            'source' => 'content_studio',
            'generated_at' => now()->toDateTimeString(),
            'mini_quiz' => $miniQuiz,
            'advanced_quiz' => $advancedQuiz,
        ]);

        return [
            'section_id' => $section->id,
            'mini_quiz_count' => count($miniQuiz),
            'advanced_quiz_count' => count($advancedQuiz),
        ];
    }

    public function generateFlashcards(MinnaLesson $lesson): array
    {
        $lesson->loadMissing('sections');
        $cards = $this->buildFlashcards($lesson);

        if ($cards === []) {
            throw ValidationException::withMessages([
                'lesson' => 'Bài này chưa có từ vựng đủ mặt trước/mặt sau để tạo flashcard.',
            ]);
        }

        $section = $this->upsertGeneratedSection($lesson, 'flashcards', 'Flashcard tự động', [
            'source' => 'content_studio',
            'generated_at' => now()->toDateTimeString(),
            'cards' => $cards,
        ]);

        return [
            'section_id' => $section->id,
            'card_count' => count($cards),
        ];
    }

    public function userPreviewData(MinnaLesson $lesson): array
    {
        $lesson->loadMissing('sections');
        $sectionsByKey = $lesson->sections->groupBy('key');

        return [
            'lesson' => $lesson,
            'sectionsByKey' => $sectionsByKey,
            'orderedKeys' => $this->orderedSectionKeys($sectionsByKey),
            'quizQuestions' => $this->buildMiniQuiz($this->extractStudyItems($lesson)),
            'advancedQuestions' => $this->advancedQuizService->build($lesson),
            'flashcards' => $this->buildFlashcards($lesson),
            'diagnostics' => $this->diagnostics($lesson),
        ];
    }

    public function versionCompare(MinnaLesson $lesson): array
    {
        $lesson = $lesson->fresh();
        $current = $lesson->attributesToArray();
        $versions = ContentVersion::query()
            ->where('versionable_type', MinnaLesson::class)
            ->where('versionable_id', $lesson->id)
            ->with('actor:id,name,email')
            ->latest()
            ->take(6)
            ->get();

        $previousVersion = $versions->skip(1)->first() ?: $versions->first();
        $previous = $previousVersion?->snapshot ?? [];
        $keys = collect(array_keys($previous))
            ->merge(array_keys($current))
            ->unique()
            ->sort()
            ->values();

        $rows = $keys->map(function (string $key) use ($previous, $current) {
            $before = $previous[$key] ?? null;
            $after = $current[$key] ?? null;

            return [
                'key' => $key,
                'before' => $before,
                'after' => $after,
                'changed' => $this->normalizedValue($before) !== $this->normalizedValue($after),
            ];
        })->values()->all();

        return [
            'previousVersion' => $previousVersion,
            'latestVersion' => $versions->first(),
            'versions' => $versions,
            'rows' => $rows,
        ];
    }

    private function createTemplateSections(MinnaLesson $lesson): void
    {
        foreach (['tu-vung', 'ngu-phap', 'luyen-doc', 'hoi-thoai', 'han-tu'] as $key) {
            $this->createSection($lesson, $key, $this->sectionTitle($key), $this->defaultContentFor($key));
        }
    }

    private function createSection(MinnaLesson $lesson, string $key, string $title, ?array $content = null): MinnaSection
    {
        $orderIndex = ((int) $lesson->sections()->max('order_index')) + 1;

        return MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => $orderIndex,
            'key' => $key,
            'title' => $title,
            'content' => $content,
            'publish_status' => PublishStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    }

    private function upsertGeneratedSection(MinnaLesson $lesson, string $key, string $title, array $content): MinnaSection
    {
        $section = $lesson->sections()->where('key', $key)->first();
        if (! $section) {
            $section = $this->createSection($lesson, $key, $title, $content);
            $section->forceFill([
                'publish_status' => PublishStatus::DRAFT,
                'published_at' => null,
            ])->save();

            return $section;
        }

        $section->forceFill([
            'title' => $title,
            'content' => $content,
            'publish_status' => PublishStatus::DRAFT,
            'published_at' => null,
        ])->save();

        return $section;
    }

    private function extractStudyItems(MinnaLesson $lesson): Collection
    {
        $lesson->loadMissing('sections');

        return $lesson->sections
            ->where('key', 'tu-vung')
            ->flatMap(function (MinnaSection $section) {
                $content = is_array($section->content) ? $section->content : [];
                $rows = [];

                foreach (self::VOCAB_KEYS as $group) {
                    foreach (($content[$group] ?? []) as $item) {
                        if (! is_array($item)) {
                            continue;
                        }

                        $japanese = trim((string) ($item['tu_vung'] ?? $item['jp'] ?? ''));
                        $meaning = trim((string) ($item['nghia'] ?? $item['meaning'] ?? ''));
                        if ($japanese === '' || $meaning === '') {
                            continue;
                        }

                        $rows[] = [
                            'japanese' => $japanese,
                            'meaning' => $meaning,
                            'reading' => trim((string) ($item['romaji'] ?? $item['reading'] ?? '')),
                            'kanji' => trim((string) ($item['han_tu'] ?? $item['kanji'] ?? '')),
                            'group' => $group,
                        ];
                    }
                }

                return $rows;
            })
            ->unique(fn (array $item) => $item['japanese'].'|'.$item['meaning'])
            ->values();
    }

    private function buildMiniQuiz(Collection $items, int $limit = 5): array
    {
        if ($items->count() < 4 || $items->pluck('meaning')->unique()->count() < 4) {
            return [];
        }

        $answers = $items->pluck('meaning')->unique()->values()->all();

        return $items->take($limit)->values()->map(function (array $item, int $index) use ($answers) {
            $options = [$item['meaning']];
            $cursor = $index + 1;
            while (count($options) < 4 && count($options) < count($answers)) {
                $candidate = $answers[$cursor % count($answers)];
                if (! in_array($candidate, $options, true)) {
                    $options[] = $candidate;
                }
                $cursor++;
            }
            sort($options);

            return [
                'id' => 'studio_mini_'.$index,
                'type' => 'multiple_choice',
                'prompt' => $item['japanese'],
                'answer' => $item['meaning'],
                'options' => $options,
            ];
        })->all();
    }

    private function buildFlashcards(MinnaLesson $lesson): array
    {
        return $this->extractStudyItems($lesson)
            ->map(function (array $item, int $index) use ($lesson) {
                $back = $item['meaning'];
                if ($item['kanji'] !== '' && $item['kanji'] !== '-') {
                    $back .= ' | Hán tự: '.$item['kanji'];
                }
                if ($item['reading'] !== '') {
                    $back .= ' | Cách đọc: '.$item['reading'];
                }

                return [
                    'id' => 'studio_card_'.$lesson->id.'_'.$index,
                    'front' => $item['japanese'],
                    'back' => $back,
                    'group' => $item['group'],
                    'lesson_number' => $lesson->number,
                ];
            })
            ->values()
            ->all();
    }

    private function missingAudioTexts(array $texts): array
    {
        if ($texts === []) {
            return [];
        }

        $language = (string) config('pronunciation.default_language', 'ja-JP');
        $hashesByText = collect($texts)
            ->mapWithKeys(fn (string $text) => [$text => $this->pronunciationService->hash($text, $language)])
            ->all();

        $existingHashes = PronunciationAudio::query()
            ->whereIn('text_hash', array_values($hashesByText))
            ->whereNotNull('audio_url')
            ->where('audio_url', '!=', '')
            ->pluck('text_hash')
            ->all();

        $existingHashes = array_flip($existingHashes);

        return collect($hashesByText)
            ->filter(fn (string $hash) => ! isset($existingHashes[$hash]))
            ->keys()
            ->values()
            ->all();
    }

    private function rowsFromFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        if (in_array($extension, ['csv', 'txt', 'tsv'], true)) {
            return $this->rowsFromCsv($file);
        }

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            if (! class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                throw ValidationException::withMessages([
                    'file' => 'Máy hiện chưa có thư viện đọc Excel. Hãy xuất file thành CSV rồi nhập lại.',
                ]);
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());

            return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        }

        throw ValidationException::withMessages([
            'file' => 'Chỉ hỗ trợ CSV, TSV, XLS hoặc XLSX.',
        ]);
    }

    private function rowsFromCsv(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $firstLine = (string) file_get_contents($path, false, null, 0, 4096);
        $delimiter = $this->detectDelimiter($firstLine);
        $rows = [];
        $handle = fopen($path, 'rb');

        if (! $handle) {
            return [];
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function normalizeRows(array $rawRows): array
    {
        $rawRows = collect($rawRows)
            ->map(fn ($row) => array_map(fn ($value) => is_string($value) ? trim($value) : $value, (array) $row))
            ->filter(fn (array $row) => collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isNotEmpty())
            ->values()
            ->all();

        if ($rawRows === []) {
            return [];
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), array_shift($rawRows));
        if ($headers === []) {
            return [];
        }

        $rows = [];
        foreach ($rawRows as $row) {
            $assoc = [];
            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }
                $assoc[$header] = trim((string) ($row[$index] ?? ''));
            }
            if (collect($assoc)->filter()->isNotEmpty()) {
                $rows[] = $assoc;
            }
        }

        return $rows;
    }

    private function mergeRowIntoSectionContent(array $content, string $sectionKey, array $row): array
    {
        return match ($sectionKey) {
            'tu-vung' => $this->mergeVocabularyRow($content, $row),
            'ngu-phap' => $this->mergeGrammarRow($content, $row),
            'luyen-doc' => $this->mergeReadingRow($content, $row),
            'hoi-thoai' => $this->mergeDialogueRow($content, $row),
            'han-tu' => $this->mergeKanjiRow($content, $row),
            default => [$content, 0],
        };
    }

    private function mergeVocabularyRow(array $content, array $row): array
    {
        $front = $this->firstValue($row, ['tu_vung', 'jp', 'japanese', 'word', 'tu']);
        $meaning = $this->firstValue($row, ['nghia', 'meaning', 'vietnamese', 'vi']);
        if ($front === '' || $meaning === '') {
            return [$content, 0];
        }

        $group = $this->normalizeVocabGroup($this->firstValue($row, ['group', 'nhom']) ?: 'vocab');
        $content[$group] = $content[$group] ?? [];
        $item = array_filter([
            'tu_vung' => $front,
            'nghia' => $meaning,
            'romaji' => $this->firstValue($row, ['romaji', 'reading', 'cach_doc']),
            'han_tu' => $this->firstValue($row, ['han_tu', 'kanji']),
            'am_han' => $this->firstValue($row, ['am_han']),
            'loai_tu' => $this->firstValue($row, ['loai_tu', 'type']),
            'ghi_chu' => $this->firstValue($row, ['ghi_chu', 'note', 'notes']),
        ], fn ($value) => $value !== '');

        $exists = collect($content[$group])->contains(function ($existing) use ($front, $meaning) {
            return is_array($existing)
                && trim((string) ($existing['tu_vung'] ?? $existing['jp'] ?? '')) === $front
                && trim((string) ($existing['nghia'] ?? '')) === $meaning;
        });

        if (! $exists) {
            $content[$group][] = $item;
        }

        return [$content, $exists ? 0 : 1];
    }

    private function mergeGrammarRow(array $content, array $row): array
    {
        $title = $this->firstValue($row, ['grammar_title', 'title', 'mau_cau', 'pattern']);
        $explain = $this->firstValue($row, ['explain', 'giai_thich', 'meaning']);
        if ($title === '' && $explain === '') {
            return [$content, 0];
        }

        $content[] = array_filter([
            'title' => $title ?: 'Mẫu ngữ pháp',
            'pattern' => $this->firstValue($row, ['pattern', 'cong_thuc']) ?: $title,
            'explain' => array_values(array_filter([$explain])),
            'examples' => $this->firstValue($row, ['example', 'vi_du'])
                ? [['jp' => $this->firstValue($row, ['example', 'vi_du']), 'nghia' => $this->firstValue($row, ['example_meaning', 'nghia_vi_du'])]]
                : [],
        ], fn ($value) => $value !== '' && $value !== []);

        return [$content, 1];
    }

    private function mergeReadingRow(array $content, array $row): array
    {
        $sentence = $this->firstValue($row, ['sentence', 'jp', 'japanese', 'cau']);
        if ($sentence === '') {
            return [$content, 0];
        }

        $content['sentences'] = array_values(array_unique(array_merge($content['sentences'] ?? [], [$sentence])));

        return [$content, 1];
    }

    private function mergeDialogueRow(array $content, array $row): array
    {
        $jp = $this->firstValue($row, ['jp', 'japanese', 'line', 'cau']);
        if ($jp === '') {
            return [$content, 0];
        }

        $content['dialogue'] = $content['dialogue'] ?? [];
        $content['dialogue'][] = array_filter([
            'speaker' => $this->firstValue($row, ['speaker', 'nguoi_noi']),
            'jp' => $jp,
            'romaji' => $this->firstValue($row, ['romaji', 'reading']),
        ], fn ($value) => $value !== '');

        return [$content, 1];
    }

    private function mergeKanjiRow(array $content, array $row): array
    {
        $kanji = $this->firstValue($row, ['kanji', 'han_tu']);
        if ($kanji === '') {
            return [$content, 0];
        }

        $content[] = array_filter([
            'kanji' => $kanji,
            'han_viet' => $this->firstValue($row, ['han_viet']),
            'nghia_vi' => $this->firstValue($row, ['nghia_vi', 'meaning', 'nghia']),
            'tu_vung' => $this->firstValue($row, ['tu_vung', 'word']),
        ], fn ($value) => $value !== '');

        return [$content, 1];
    }

    private function orderedSectionKeys(Collection $sectionsByKey): array
    {
        $order = ['tu-vung', 'ngu-phap', 'luyen-doc', 'hoi-thoai', 'han-tu', 'quiz', 'flashcards'];
        $keys = [];

        foreach ($order as $key) {
            if ($sectionsByKey->has($key)) {
                $keys[] = $key;
            }
        }

        foreach ($sectionsByKey->keys() as $key) {
            if (! in_array($key, $keys, true)) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    private function defaultContentFor(string $key): ?array
    {
        return match ($key) {
            'tu-vung' => ['vocab' => []],
            'luyen-doc' => ['sentences' => []],
            'hoi-thoai' => ['dialogue' => []],
            default => [],
        };
    }

    private function sectionTitle(string $key): string
    {
        return [
            'tu-vung' => 'Phần 1: Từ vựng',
            'ngu-phap' => 'Phần 2: Ngữ pháp',
            'luyen-doc' => 'Phần 3: Luyện đọc',
            'hoi-thoai' => 'Phần 4: Hội thoại',
            'han-tu' => 'Phần 5: Hán tự',
            'quiz' => 'Quiz tự động',
            'flashcards' => 'Flashcard tự động',
        ][$key] ?? Str::headline(str_replace('-', ' ', $key));
    }

    private function normalizeSectionKey(string $value): string
    {
        $key = $this->normalizeHeader($value);

        return [
            'tu_vung' => 'tu-vung',
            'vocab' => 'tu-vung',
            'vocabulary' => 'tu-vung',
            'ngu_phap' => 'ngu-phap',
            'grammar' => 'ngu-phap',
            'luyen_doc' => 'luyen-doc',
            'reading' => 'luyen-doc',
            'hoi_thoai' => 'hoi-thoai',
            'dialogue' => 'hoi-thoai',
            'conversation' => 'hoi-thoai',
            'han_tu' => 'han-tu',
            'kanji' => 'han-tu',
        ][$key] ?? str_replace('_', '-', $key);
    }

    private function normalizeVocabGroup(string $value): string
    {
        $group = $this->normalizeHeader($value);

        return in_array($group, self::VOCAB_KEYS, true) ? $group : 'vocab';
    }

    private function normalizeHeader(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
        $value = Str::ascii(Str::lower(trim($value)));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value) ?? '';

        return trim($value, '_');
    }

    private function firstValue(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            $normalized = $this->normalizeHeader($key);
            $value = trim((string) ($row[$normalized] ?? $row[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function detectDelimiter(string $line): string
    {
        $candidates = [',' => substr_count($line, ','), ';' => substr_count($line, ';'), "\t" => substr_count($line, "\t")];
        arsort($candidates);

        return (string) array_key_first($candidates);
    }

    private function nextLessonNumber(): int
    {
        return ((int) MinnaLesson::query()->max('number')) + 1;
    }

    private function normalizedValue(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }
}
