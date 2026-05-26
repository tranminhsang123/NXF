<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pronunciation Provider
    |--------------------------------------------------------------------------
    |
    | browser: client uses Web Speech API when no cached audio exists.
    | manual:  use rows in pronunciation_audios only.
    | google:  generate MP3 through Google Cloud Text-to-Speech.
    | azure:   generate MP3 through Azure AI Speech.
    | forvo:   resolve crowd-sourced MP3 URLs through the Forvo API.
    |
    */
    'provider' => env('PRONUNCIATION_PROVIDER', 'browser'),
    'default_language' => env('PRONUNCIATION_LANGUAGE', 'ja-JP'),

    'google' => [
        'api_key' => env('GOOGLE_TTS_API_KEY'),
        'endpoint' => env('GOOGLE_TTS_ENDPOINT', 'https://texttospeech.googleapis.com/v1/text:synthesize'),
        'voice' => env('GOOGLE_TTS_VOICE', 'ja-JP-Neural2-B'),
        'speaking_rate' => (float) env('GOOGLE_TTS_SPEAKING_RATE', 0.9),
    ],

    'azure' => [
        'key' => env('AZURE_SPEECH_KEY'),
        'region' => env('AZURE_SPEECH_REGION'),
        'endpoint' => env('AZURE_SPEECH_ENDPOINT'),
        'voice' => env('AZURE_SPEECH_VOICE', 'ja-JP-NanamiNeural'),
        'output_format' => env('AZURE_SPEECH_OUTPUT_FORMAT', 'audio-16khz-32kbitrate-mono-mp3'),
    ],

    'forvo' => [
        'api_key' => env('FORVO_API_KEY'),
        'endpoint' => env('FORVO_API_ENDPOINT', 'https://apifree.forvo.com/key/{key}/format/json/action/word-pronunciations/word/{word}/language/{language}/'),
    ],
];
