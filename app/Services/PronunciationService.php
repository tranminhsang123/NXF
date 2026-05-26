<?php

namespace App\Services;

use App\Models\PronunciationAudio;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PronunciationService
{
    public function resolve(string $text, ?string $language = null): array
    {
        $normalized = $this->normalizeText($text);
        $language = $language ?: (string) config('pronunciation.default_language', 'ja-JP');
        $provider = (string) config('pronunciation.provider', 'browser');

        if ($normalized === '') {
            return [
                'text' => '',
                'language' => $language,
                'provider' => $provider,
                'audio_url' => null,
                'fallback' => 'browser',
            ];
        }

        $textHash = $this->hash($normalized, $language);
        $record = PronunciationAudio::query()
            ->where('text_hash', $textHash)
            ->first();

        if ($record) {
            $record->increment('usage_count');
            $record->forceFill(['last_used_at' => now()])->saveQuietly();

            return [
                'text' => $record->text,
                'language' => $record->language,
                'provider' => $record->source,
                'audio_url' => $record->audio_url,
                'fallback' => $record->audio_url ? null : 'browser',
            ];
        }

        $generated = $this->resolveFromProvider($normalized, $language, $provider);

        if ($generated) {
            $record = PronunciationAudio::query()->create([
                'text_hash' => $textHash,
                'text' => $normalized,
                'language' => $language,
                'source' => $generated['source'],
                'audio_url' => $generated['audio_url'],
                'metadata' => $generated['metadata'] ?? [],
                'usage_count' => 1,
                'last_used_at' => now(),
            ]);

            return [
                'text' => $record->text,
                'language' => $record->language,
                'provider' => $record->source,
                'audio_url' => $record->audio_url,
                'fallback' => null,
            ];
        }

        return [
            'text' => $normalized,
            'language' => $language,
            'provider' => $provider,
            'audio_url' => null,
            'fallback' => 'browser',
        ];
    }

    public function normalizeText(string $text): string
    {
        $text = trim(strip_tags($text));
        $text = preg_replace('/\s+/u', ' ', $text) ?: '';

        return mb_substr($text, 0, 500);
    }

    public function hash(string $text, string $language): string
    {
        return hash('sha256', $language.'|'.$this->normalizeText($text));
    }

    private function resolveFromProvider(string $text, string $language, string $provider): ?array
    {
        return match (strtolower($provider)) {
            'google' => $this->resolveGoogle($text, $language),
            'azure' => $this->resolveAzure($text, $language),
            'forvo' => $this->resolveForvo($text, $language),
            default => null,
        };
    }

    private function resolveGoogle(string $text, string $language): ?array
    {
        $apiKey = (string) config('pronunciation.google.api_key', '');

        if ($apiKey === '') {
            return null;
        }

        $endpoint = (string) config('pronunciation.google.endpoint');
        $voice = (string) config('pronunciation.google.voice', 'ja-JP-Neural2-B');
        $speakingRate = (float) config('pronunciation.google.speaking_rate', 0.9);

        try {
            $url = $endpoint.(str_contains($endpoint, '?') ? '&' : '?').http_build_query(['key' => $apiKey]);
            $response = Http::timeout(12)->asJson()->post($url, [
                'input' => ['text' => $text],
                'voice' => array_filter([
                    'languageCode' => $language,
                    'name' => $voice ?: null,
                ]),
                'audioConfig' => [
                    'audioEncoding' => 'MP3',
                    'speakingRate' => $speakingRate,
                ],
            ]);

            if (! $response->ok()) {
                return null;
            }

            $audioContent = $response->json('audioContent');
            $binary = is_string($audioContent) ? base64_decode($audioContent, true) : false;

            if ($binary === false || $binary === '') {
                return null;
            }

            $audioUrl = $this->storeGeneratedAudio('google', $text, $language, $binary);

            return $audioUrl ? [
                'source' => 'google',
                'audio_url' => $audioUrl,
                'metadata' => [
                    'voice' => $voice,
                    'speaking_rate' => $speakingRate,
                    'audio_encoding' => 'MP3',
                ],
            ] : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function resolveAzure(string $text, string $language): ?array
    {
        $key = (string) config('pronunciation.azure.key', '');
        $region = (string) config('pronunciation.azure.region', '');

        if ($key === '' || $region === '') {
            return null;
        }

        $endpoint = (string) config('pronunciation.azure.endpoint')
            ?: "https://{$region}.tts.speech.microsoft.com/cognitiveservices/v1";
        $voice = (string) config('pronunciation.azure.voice', 'ja-JP-NanamiNeural');
        $outputFormat = (string) config('pronunciation.azure.output_format', 'audio-16khz-32kbitrate-mono-mp3');
        $escapedText = htmlspecialchars($text, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        $ssml = '<speak version="1.0" xml:lang="'.$language.'"><voice xml:lang="'.$language.'" name="'.$voice.'">'.$escapedText.'</voice></speak>';

        try {
            $response = Http::timeout(12)
                ->withHeaders([
                    'Ocp-Apim-Subscription-Key' => $key,
                    'X-Microsoft-OutputFormat' => $outputFormat,
                    'User-Agent' => config('app.name', 'Japanese Study'),
                ])
                ->withBody($ssml, 'application/ssml+xml')
                ->post($endpoint);

            if (! $response->ok() || $response->body() === '') {
                return null;
            }

            $audioUrl = $this->storeGeneratedAudio('azure', $text, $language, $response->body());

            return $audioUrl ? [
                'source' => 'azure',
                'audio_url' => $audioUrl,
                'metadata' => [
                    'voice' => $voice,
                    'output_format' => $outputFormat,
                ],
            ] : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function resolveForvo(string $text, string $language): ?array
    {
        $apiKey = (string) config('pronunciation.forvo.api_key', '');

        if ($apiKey === '') {
            return null;
        }

        $languageCode = $this->forvoLanguageCode($language);
        $endpoint = (string) config('pronunciation.forvo.endpoint');

        try {
            if (str_contains($endpoint, '{')) {
                $response = Http::timeout(12)->get(strtr($endpoint, [
                    '{key}' => rawurlencode($apiKey),
                    '{word}' => rawurlencode($text),
                    '{language}' => rawurlencode($languageCode),
                ]));
            } else {
                $response = Http::timeout(12)->get($endpoint, [
                    'key' => $apiKey,
                    'format' => 'json',
                    'action' => 'word-pronunciations',
                    'word' => $text,
                    'language' => $languageCode,
                ]);
            }

            if (! $response->ok()) {
                return null;
            }

            foreach (($response->json('items') ?? []) as $item) {
                $audioUrl = $item['pathmp3'] ?? $item['url'] ?? $item['pathogg'] ?? null;

                if (is_string($audioUrl) && $audioUrl !== '') {
                    return [
                        'source' => 'forvo',
                        'audio_url' => $audioUrl,
                        'metadata' => [
                            'language' => $languageCode,
                            'username' => $item['username'] ?? null,
                            'country' => $item['country'] ?? null,
                            'rate' => $item['rate'] ?? null,
                        ],
                    ];
                }
            }

            return null;
        } catch (Throwable) {
            return null;
        }
    }

    private function storeGeneratedAudio(string $provider, string $text, string $language, string $binary): ?string
    {
        $path = 'pronunciation/'.$provider.'-'.$this->hash($text, $language).'.mp3';

        if (! Storage::disk('public')->put($path, $binary)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    private function forvoLanguageCode(string $language): string
    {
        $normalized = strtolower(str_replace('_', '-', $language));
        $primary = explode('-', $normalized)[0] ?? 'ja';

        return $primary !== '' ? $primary : 'ja';
    }
}
