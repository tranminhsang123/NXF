<?php

namespace App\Providers;

use App\Models\Alphabet;
use App\Models\Kanji;
use App\Models\LogoSetting;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\N5CourseData;
use App\Observers\AdminContentObserver;
use App\Observers\AlphabetObserver;
use App\Observers\KanjiObserver;
use App\Observers\MinnaLessonObserver;
use App\Observers\MinnaSectionObserver;
use App\Observers\N5CourseDataObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Kanji::observe(KanjiObserver::class);
        Alphabet::observe(AlphabetObserver::class);
        N5CourseData::observe(N5CourseDataObserver::class);
        MinnaLesson::observe(MinnaLessonObserver::class);
        MinnaSection::observe(MinnaSectionObserver::class);

        foreach ([Alphabet::class, Kanji::class, N5CourseData::class, MinnaLesson::class, MinnaSection::class] as $model) {
            $model::observe(AdminContentObserver::class);
        }

        View::composer(['layouts.header', 'adminlayout.app'], function ($view) {
            $view->with('siteLogoUrl', LogoSetting::currentLogoUrl());
            $view->with('siteLogoTitle', LogoSetting::currentTitle());
            $view->with('siteLogoSubtitle', LogoSetting::currentSubtitle());
        });

        Blade::if('adminCan', function (string $permission): bool {
            $user = auth()->user();

            return $user && $user->hasAdminPermission($permission);
        });
    }
}
