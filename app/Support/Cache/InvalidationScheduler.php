<?php

namespace App\Support\Cache;

/**
 * Gom invalidation cache: mỗi key chỉ chạy callback một lần mỗi request, thực thi cuối request (terminating).
 * Hỗ trợ lồng nhau: callback đăng ký thêm key khác sẽ chạy ngay khi đang trong phase run().
 */
final class InvalidationScheduler
{
    /** @var array<string, callable> */
    private static array $callbacks = [];

    private static bool $registered = false;

    private static bool $running = false;

    /**
     * Đăng ký callback chạy tối đa một lần cho $key trong request hiện tại (cuối request).
     */
    public static function once(string $key, callable $callback): void
    {
        if (array_key_exists($key, self::$callbacks)) {
            return;
        }

        self::$callbacks[$key] = $callback;

        if (self::$running) {
            self::executeKey($key);

            return;
        }

        if (! self::$registered) {
            self::$registered = true;
            app()->terminating(function () {
                self::runPending();
            });
        }
    }

    private static function executeKey(string $key): void
    {
        if (! array_key_exists($key, self::$callbacks)) {
            return;
        }

        $cb = self::$callbacks[$key];
        unset(self::$callbacks[$key]);
        $cb();
    }

    private static function runPending(): void
    {
        self::$running = true;

        try {
            while (($key = array_key_first(self::$callbacks)) !== null) {
                self::executeKey($key);
            }
        } finally {
            self::$running = false;
            self::$registered = false;
        }
    }
}
