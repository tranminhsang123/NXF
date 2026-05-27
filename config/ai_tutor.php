<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Tutor Provider
    |--------------------------------------------------------------------------
    |
    | local:  chạy bằng logic có kiểm soát, không cần mạng/API key.
    | openai: gọi OpenAI Responses API nếu OPENAI_API_KEY có trong .env.
    |
    */

    'provider' => env('AI_TUTOR_PROVIDER', 'local'),

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('AI_TUTOR_OPENAI_MODEL', 'gpt-4o-mini'),
        'endpoint' => env('AI_TUTOR_OPENAI_ENDPOINT', 'https://api.openai.com/v1/responses'),
        'timeout' => (int) env('AI_TUTOR_TIMEOUT', 20),
    ],

    'max_context_items' => (int) env('AI_TUTOR_MAX_CONTEXT_ITEMS', 8),
];
