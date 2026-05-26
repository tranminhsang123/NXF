<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePronunciationAudioJob;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\PronunciationAudio;
use App\Services\AdminAuditService;
use App\Services\PronunciationService;
use Illuminate\Http\Request;

class AudioManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = PronunciationAudio::query()->latest();

        if ($request->filled('source')) {
            $query->where('source', (string) $request->query('source'));
        }
        if ($request->filled('q')) {
            $q = (string) $request->query('q');
            $query->where('text', 'like', '%'.$q.'%');
        }

        $audios = $query->paginate(25)->withQueryString();
        $provider = (string) config('pronunciation.provider', 'browser');
        $providerHealth = [
            'active' => $provider,
            'google' => (bool) config('pronunciation.google.api_key'),
            'azure' => (bool) (config('pronunciation.azure.key') && config('pronunciation.azure.region')),
            'forvo' => (bool) config('pronunciation.forvo.api_key'),
        ];

        return view('admin.audio.index', compact('audios', 'providerHealth'));
    }

    public function generate(Request $request, PronunciationService $service)
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:500'],
            'language' => ['nullable', 'string', 'max:16'],
            'force' => ['nullable', 'boolean'],
        ]);

        $language = $data['language'] ?? (string) config('pronunciation.default_language', 'ja-JP');
        $text = $service->normalizeText($data['text']);
        $textHash = $service->hash($text, $language);

        if ($request->boolean('force')) {
            PronunciationAudio::query()
                ->where('text_hash', $textHash)
                ->delete();
        }

        $audio = $service->resolve($text, $language);
        $audioRecord = PronunciationAudio::query()->where('text_hash', $textHash)->first();

        app(AdminAuditService::class)->audit(
            $request->user(),
            $audioRecord,
            'audio_generated',
            'Đã xử lý audio/TTS: '.$text,
            null,
            [
                'text' => $audio['text'] ?? $text,
                'language' => $audio['language'] ?? $language,
                'provider' => $audio['provider'] ?? null,
                'audio_url' => $audio['audio_url'] ?? null,
                'fallback' => $audio['fallback'] ?? null,
            ],
            ['force' => $request->boolean('force')]
        );

        return back()->with('success', 'Đã xử lý audio bằng '.$audio['provider'].($audio['audio_url'] ? '.' : ' và dùng phát âm trình duyệt.'));
    }

    public function bulkGenerate(Request $request)
    {
        $data = $request->validate([
            'lesson_number' => ['required', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
            'force' => ['nullable', 'boolean'],
        ]);

        $lesson = MinnaLesson::query()->where('number', (int) $data['lesson_number'])->firstOrFail();
        $section = MinnaSection::query()
            ->where('lesson_id', $lesson->id)
            ->where('key', 'tu-vung')
            ->first();

        $texts = $this->extractPronunciationTexts($section?->content ?? []);
        $texts = array_slice(array_values(array_unique($texts)), 0, (int) ($data['limit'] ?? 80));
        $language = (string) config('pronunciation.default_language', 'ja-JP');
        foreach ($texts as $text) {
            GeneratePronunciationAudioJob::dispatch($text, $language, $request->boolean('force'));
        }

        app(AdminAuditService::class)->audit(
            $request->user(),
            $lesson,
            'audio_bulk_generate_queued',
            'Đã đưa audio bài '.$lesson->number.' vào hàng đợi.',
            null,
            null,
            [
                'lesson_number' => $lesson->number,
                'text_count' => count($texts),
                'language' => $language,
                'force' => $request->boolean('force'),
            ]
        );

        return back()->with('success', 'Đã đưa '.count($texts).' audio của bài '.$lesson->number.' vào hàng đợi xử lý.');
    }

    public function destroy(Request $request, PronunciationAudio $audio)
    {
        $before = $audio->only(['id', 'text', 'language', 'source', 'audio_url']);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $audio,
            'audio_deleted',
            'Đã xóa cache audio/TTS: '.$audio->text,
            $before
        );

        $audio->delete();

        return back()->with('success', 'Đã xóa cache audio.');
    }

    private function extractPronunciationTexts(array $content): array
    {
        $texts = [];
        $walk = function ($value) use (&$walk, &$texts) {
            if (is_array($value)) {
                foreach ($value as $key => $child) {
                    if (in_array($key, ['tu_vung', 'jp', 'japanese', 'cau', 'front'], true) && is_string($child)) {
                        $child = trim($child);
                        if ($child !== '') {
                            $texts[] = $child;
                        }
                    }
                    $walk($child);
                }
            }
        };
        $walk($content);

        return $texts;
    }
}
