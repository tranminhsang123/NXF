<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Write Mode
    |--------------------------------------------------------------------------
    |
    | - normal:            write + broadcast
    | - degrade_no_broadcast: write DB but skip realtime broadcast
    | - disable_write:     reject write endpoints gracefully
    |
    */
    'write_mode' => env('CHAT_WRITE_MODE', 'normal'),

    /*
    |--------------------------------------------------------------------------
    | Idempotency TTL (hours)
    |--------------------------------------------------------------------------
    |
    | Keys older than this window can be cleared from message rows to avoid
    | unbounded growth and allow clients to rotate IDs safely.
    |
    */
    'idempotency_ttl_hours' => (int) env('CHAT_IDEMPOTENCY_TTL_HOURS', 72),
];
