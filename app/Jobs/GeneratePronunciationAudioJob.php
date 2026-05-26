<?php

namespace App\Jobs;

use App\Models\PronunciationAudio;
use App\Services\PronunciationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePronunciationAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $text,
        public string $language = 'ja-JP',
        public bool $force = false
    ) {}

    public function handle(PronunciationService $service): void
    {
        $text = $service->normalizeText($this->text);

        if ($text === '') {
            return;
        }

        if ($this->force) {
            PronunciationAudio::query()
                ->where('text_hash', $service->hash($text, $this->language))
                ->delete();
        }

        $service->resolve($text, $this->language);
    }
}
