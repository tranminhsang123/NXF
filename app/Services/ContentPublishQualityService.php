<?php

namespace App\Services;

use App\Models\ContentErrorReport;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\PronunciationAudio;
use App\Support\AdminContentRegistry;
use Illuminate\Database\Eloquent\Model;

class ContentPublishQualityService
{
    private const VOCAB_KEYS = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];

    public function __construct(
        private ContentValidationService $validationService,
        private PronunciationService $pronunciationService,
        private AdvancedQuizService $advancedQuizService
    ) {}

    public function checklist(Model $item): array
    {
        $items = [
            $this->validationItem($item),
            $this->openReportItem($item),
        ];

        if ($item instanceof MinnaLesson) {
            $items = array_merge($items, $this->minnaLessonItems($item));
        }

        $blocking = collect($items)
            ->filter(fn (array $item) => ($item['required'] ?? true) && $item['status'] === 'fail')
            ->values();

        return [
            'passed' => $blocking->isEmpty(),
            'blocking_count' => $blocking->count(),
            'items' => $items,
        ];
    }

    public function blockingMessages(Model $item): array
    {
        return collect($this->checklist($item)['items'])
            ->filter(fn (array $item) => ($item['required'] ?? true) && $item['status'] === 'fail')
            ->map(fn (array $item) => $item['label'].': '.$item['summary'])
            ->values()
            ->all();
    }

    private function validationItem(Model $item): array
    {
        $issues = $this->validationService->validate($item);

        return [
            'key' => 'content_validation',
            'label' => 'Kiểm tra dữ liệu cơ bản',
            'status' => empty($issues) ? 'pass' : 'fail',
            'summary' => empty($issues) ? 'Không phát hiện lỗi dữ liệu.' : count($issues).' lỗi dữ liệu cần sửa.',
            'details' => $issues,
            'required' => true,
        ];
    }

    private function openReportItem(Model $item): array
    {
        $subjects = $this->reportSubjectsFor($item);
        $count = ContentErrorReport::query()
            ->whereIn('status', [
                ContentErrorReport::STATUS_PENDING,
                ContentErrorReport::STATUS_IN_PROGRESS,
            ])
            ->where(function ($query) use ($subjects) {
                foreach ($subjects as $subject) {
                    $query->orWhere(function ($inner) use ($subject) {
                        $inner->where('content_type', $subject['type'])
                            ->where('content_id', $subject['id']);
                    });
                }
            })
            ->count();

        return [
            'key' => 'open_content_reports',
            'label' => 'Báo lỗi nội dung',
            'status' => $count === 0 ? 'pass' : 'fail',
            'summary' => $count === 0 ? 'Không còn báo lỗi đang mở.' : $count.' báo lỗi đang chờ/đang xử lý.',
            'details' => [],
            'required' => true,
            'meta' => ['open_reports' => $count],
        ];
    }

    private function minnaLessonItems(MinnaLesson $lesson): array
    {
        $lesson->loadMissing('sections');
        $publishedSections = $lesson->sections
            ->filter(fn (MinnaSection $section) => ($section->publish_status ?? 'published') === 'published')
            ->values();
        $studyItems = $this->extractStudyItems($publishedSections);
        $audioTexts = $studyItems
            ->pluck('japanese')
            ->map(fn (string $text) => $this->pronunciationService->normalizeText($text))
            ->filter()
            ->unique()
            ->values();

        $missingAudio = $this->missingAudioTexts($audioTexts->all());
        $uniqueMeanings = $studyItems->pluck('meaning')->unique()->count();
        $advancedQuestions = $this->advancedQuizService->build($lesson);

        return [
            [
                'key' => 'minna_published_sections',
                'label' => 'Phần học công khai',
                'status' => $publishedSections->isNotEmpty() ? 'pass' : 'fail',
                'summary' => $publishedSections->isNotEmpty()
                    ? 'Có '.$publishedSections->count().' phần học đang ở trạng thái xuất bản.'
                    : 'Chưa có phần học nào được xuất bản.',
                'details' => [],
                'required' => true,
                'meta' => ['published_sections' => $publishedSections->count()],
            ],
            [
                'key' => 'minna_vocabulary',
                'label' => 'Từ vựng',
                'status' => $studyItems->count() > 0 ? 'pass' : 'fail',
                'summary' => $studyItems->count() > 0
                    ? 'Có '.$studyItems->count().' mục từ/câu học.'
                    : 'Chưa có từ vựng hoặc mẫu câu có nghĩa.',
                'details' => [],
                'required' => true,
                'meta' => ['study_items' => $studyItems->count()],
            ],
            [
                'key' => 'minna_audio',
                'label' => 'Audio phát âm',
                'status' => $audioTexts->isNotEmpty() && empty($missingAudio) ? 'pass' : 'fail',
                'summary' => $audioTexts->isEmpty()
                    ? 'Chưa có mục nào cần audio.'
                    : (empty($missingAudio)
                        ? 'Đủ audio cho '.$audioTexts->count().' mục.'
                        : 'Thiếu audio cho '.count($missingAudio).'/'.$audioTexts->count().' mục.'),
                'details' => array_slice($missingAudio, 0, 8),
                'required' => true,
                'meta' => ['required_audio' => $audioTexts->count(), 'missing_audio' => count($missingAudio)],
            ],
            [
                'key' => 'minna_quiz',
                'label' => 'Quiz',
                'status' => $studyItems->count() >= 4 && $uniqueMeanings >= 4 && count($advancedQuestions) >= 3 ? 'pass' : 'fail',
                'summary' => $studyItems->count() >= 4 && $uniqueMeanings >= 4 && count($advancedQuestions) >= 3
                    ? 'Đủ dữ liệu cho mini quiz và quiz nâng cao.'
                    : 'Cần tối thiểu 4 mục/4 nghĩa khác nhau cho mini quiz và 3 câu quiz nâng cao.',
                'details' => [
                    'Mục học: '.$studyItems->count(),
                    'Nghĩa khác nhau: '.$uniqueMeanings,
                    'Câu quiz nâng cao tạo được: '.count($advancedQuestions),
                ],
                'required' => true,
                'meta' => [
                    'study_items' => $studyItems->count(),
                    'unique_meanings' => $uniqueMeanings,
                    'advanced_questions' => count($advancedQuestions),
                ],
            ],
        ];
    }

    private function extractStudyItems($sections)
    {
        return $sections
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
                        $meaning = trim((string) ($item['nghia'] ?? ''));

                        if ($japanese === '' || $meaning === '') {
                            continue;
                        }

                        $rows[] = [
                            'japanese' => $japanese,
                            'meaning' => $meaning,
                            'group' => $group,
                        ];
                    }
                }

                return $rows;
            })
            ->unique(fn (array $item) => $item['japanese'].'|'.$item['meaning'])
            ->values();
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

    private function reportSubjectsFor(Model $item): array
    {
        $type = AdminContentRegistry::typeFor($item) ?? 'unknown';
        $subjects = [
            ['type' => $type, 'id' => $item->getKey()],
        ];

        if ($item instanceof MinnaLesson) {
            foreach ($item->sections()->pluck('id') as $sectionId) {
                $subjects[] = ['type' => 'minna_section', 'id' => (int) $sectionId];
            }
        }

        return $subjects;
    }
}
