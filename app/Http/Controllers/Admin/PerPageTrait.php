<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

trait PerPageTrait
{
    protected function adminPerPage(Request $request): int
    {
        $allowed = [10, 20, 50];
        $default = 20;

        $n = $request->integer('per_page', $default);

        return in_array($n, $allowed, true) ? $n : $default;
    }
}
