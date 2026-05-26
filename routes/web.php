<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->middleware('throttle:study-get')->name('home');

// Auth Routes (throttle: 5 req/phút cho POST — tránh brute force)
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post')
        ->middleware('throttle:login');
    Route::get('/login/google', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

    Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.post')
        ->middleware('throttle:register');
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Public học thử (admin có thể khóa/mở từng phần trong trang Security)
Route::get('/alphabet', [App\Http\Controllers\UserAlphabetController::class, 'index'])
    ->middleware(['throttle:study-get', 'feature.access:alphabet'])
    ->name('alphabet.index');

Route::prefix('kanji')->name('kanji.')->group(function () {
    Route::get('/', [App\Http\Controllers\UserKanjiController::class, 'index'])
        ->middleware(['throttle:study-get', 'feature.access:kanji'])
        ->name('index');

    Route::get('/{level}', [App\Http\Controllers\UserKanjiController::class, 'list'])
        ->middleware(['throttle:study-get', 'feature.access:kanji'])
        ->name('list')
        ->where('level', 'N[1-5]');

    Route::get('/{level}/flashcard', [App\Http\Controllers\UserKanjiController::class, 'flashcard'])
        ->middleware(['throttle:study-get', 'feature.access:kanji'])
        ->name('flashcard')
        ->where('level', 'N[1-5]');
});

Route::get('/tu-vung', [App\Http\Controllers\VocabularyController::class, 'index'])
    ->middleware(['throttle:study-get', 'feature.access:vocabulary'])
    ->name('vocabulary.index');

Route::get('/tu-vung/bai-{number}', [App\Http\Controllers\VocabularyController::class, 'show'])
    ->middleware(['throttle:study-get', 'feature.access:vocabulary'])
    ->name('vocabulary.show')
    ->where('number', '[0-9]+');

Route::get('/courses', [App\Http\Controllers\CourseController::class, 'index'])
    ->middleware(['throttle:study-get', 'feature.access:course'])
    ->name('course.index');

Route::get('/course/{level}', [App\Http\Controllers\CourseController::class, 'show'])
    ->middleware(['throttle:study-get', 'feature.access:course'])
    ->name('course.show');

Route::get('/course/{level}/{sectionType}', [App\Http\Controllers\CourseController::class, 'showSection'])
    ->middleware(['throttle:study-get', 'feature.access:course'])
    ->name('course.section');

Route::get('/course/{level}/luyen-doc/{id}', [App\Http\Controllers\CourseController::class, 'showLuyenDocDetail'])
    ->middleware(['throttle:study-get', 'feature.access:course'])
    ->name('course.luyen-doc.detail');

Route::get('/course/{level}/marugoto-n5/{id}', [App\Http\Controllers\CourseController::class, 'showMarugotoDetail'])
    ->middleware(['throttle:study-get', 'feature.access:course'])
    ->name('course.marugoto.detail');

Route::get('/course/{level}/speed-master-n5/{bai}', [App\Http\Controllers\CourseController::class, 'showSpeedMasterDetail'])
    ->middleware(['throttle:study-get', 'feature.access:course'])
    ->name('course.speed-master.detail');

Route::prefix('minna')->name('minna.')->group(function () {
    Route::get('/', [App\Http\Controllers\MinnaController::class, 'index'])
        ->middleware(['throttle:study-get', 'feature.access:minna'])
        ->name('index');

    Route::get('/lo-trinh', [App\Http\Controllers\MinnaController::class, 'roadmap'])
        ->middleware(['throttle:study-get', 'feature.access:minna'])
        ->name('roadmap');

    Route::get('/bai-{number}', [App\Http\Controllers\MinnaController::class, 'show'])
        ->middleware(['throttle:study-get', 'feature.access:minna'])
        ->name('show');

    Route::get('/bai-{number}/quiz-nang-cao', [App\Http\Controllers\MinnaController::class, 'advancedQuiz'])
        ->middleware(['auth', 'throttle:study-get', 'feature.access:minna'])
        ->name('quiz.advanced');

    Route::get('/bai-{number}/{sectionKey}', [App\Http\Controllers\MinnaController::class, 'showSection'])
        ->middleware(['throttle:study-get', 'feature.access:minna'])
        ->name('section');
});

Route::get('/flashcard', [App\Http\Controllers\FlashcardController::class, 'index'])
    ->middleware(['throttle:study-get', 'feature.access:flashcard'])
    ->name('flashcard.index');

Route::get('/flashcard/bai-{number}', [App\Http\Controllers\FlashcardController::class, 'study'])
    ->middleware(['throttle:study-get', 'feature.access:flashcard'])
    ->name('flashcard.study');

Route::get('/flashcard/study', [App\Http\Controllers\FlashcardController::class, 'study'])
    ->middleware(['throttle:study-get', 'feature.access:flashcard'])
    ->name('flashcard.study.multi');

Route::get('/dictionary/lookup', [App\Http\Controllers\DictionaryController::class, 'lookup'])
    ->middleware('throttle:study-get')
    ->name('dictionary.lookup');

Route::get('/pronunciation/resolve', [App\Http\Controllers\PronunciationController::class, 'resolve'])
    ->middleware('throttle:study-get')
    ->name('pronunciation.resolve');

Route::middleware('auth')->group(function () {
    // Báo cáo vi phạm DevTools (chỉ user thường; admin không bị ghi)
    Route::post('/devtools-violation', [App\Http\Controllers\DevtoolsViolationController::class, '__invoke'])
        ->middleware('throttle:devtools-violation')
        ->name('devtools.violation');

    Route::post('/content-reports', [App\Http\Controllers\ContentErrorReportController::class, 'store'])
        ->middleware('throttle:study-post')
        ->name('content-reports.store');

    Route::post('/learning-events', [App\Http\Controllers\LearningEventController::class, 'store'])
        ->middleware('throttle:study-post')
        ->name('learning-events.store');

    // User Dashboard
    Route::get('/onboarding', [App\Http\Controllers\OnboardingController::class, 'edit'])
        ->name('onboarding.edit');
    Route::get('/onboarding/result', [App\Http\Controllers\OnboardingController::class, 'result'])
        ->middleware('throttle:study-get')
        ->name('onboarding.result');
    Route::post('/onboarding', [App\Http\Controllers\OnboardingController::class, 'update'])
        ->middleware('throttle:study-post')
        ->name('onboarding.update');

    Route::get('/dashboard', [App\Http\Controllers\UserController::class, 'dashboard'])
        ->name('user.dashboard');

    Route::get('/quick-win/chuc-mung', [App\Http\Controllers\QuickWinController::class, 'congrats'])
        ->middleware('throttle:study-get')
        ->name('quick-win.congrats');

    Route::get('/leaderboard', [App\Http\Controllers\LeaderboardController::class, 'index'])
        ->middleware('throttle:study-get')
        ->name('leaderboard.index');

    Route::get('/achievements/share', [App\Http\Controllers\AchievementShareController::class, 'show'])
        ->middleware('throttle:study-get')
        ->name('achievements.share');

    Route::get('/study-room', [App\Http\Controllers\StudyRoomController::class, 'index'])
        ->middleware('throttle:study-get')
        ->name('study-room.index');

    Route::get('/dashboard/progress', [App\Http\Controllers\UserController::class, 'progress'])
        ->name('user.progress');

    Route::get('/dashboard/statistics', [App\Http\Controllers\UserController::class, 'statistics'])
        ->name('user.statistics');

    Route::get('/dashboard/activity', [App\Http\Controllers\UserController::class, 'activity'])
        ->name('user.activity');

    Route::prefix('inbox')->name('inbox.')->group(function () {
        Route::get('/', [App\Http\Controllers\DirectInboxController::class, 'index'])
            ->middleware('throttle:study-get')
            ->name('index');
        Route::get('/unread-count', [App\Http\Controllers\DirectInboxController::class, 'unreadCount'])
            ->middleware('throttle:study-get')
            ->name('unread-count');
        Route::get('/{conversation}', [App\Http\Controllers\DirectInboxController::class, 'show'])
            ->middleware('throttle:study-get')
            ->name('show')
            ->where('conversation', '[0-9]+');
        Route::get('/{conversation}/messages', [App\Http\Controllers\DirectInboxController::class, 'fetch'])
            ->middleware('throttle:study-get')
            ->name('messages.fetch')
            ->where('conversation', '[0-9]+');
        Route::post('/{conversation}/messages', [App\Http\Controllers\DirectInboxController::class, 'store'])
            ->middleware(['throttle:study-post', 'throttle:chat-write'])
            ->name('messages.store')
            ->where('conversation', '[0-9]+');
    });

    Route::post('/flashcard/review', [App\Http\Controllers\FlashcardController::class, 'review'])
        ->middleware('throttle:study-post')
        ->name('flashcard.review');

    Route::get('/flashcard/favorites', [App\Http\Controllers\FlashcardController::class, 'favorites'])
        ->middleware('throttle:study-get')
        ->name('flashcard.favorites');

    Route::post('/favorites', [App\Http\Controllers\FavoriteItemController::class, 'store'])
        ->middleware('throttle:study-post')
        ->name('favorites.store');

    Route::delete('/favorites/{favoriteItem}', [App\Http\Controllers\FavoriteItemController::class, 'destroy'])
        ->middleware('throttle:study-post')
        ->name('favorites.destroy');

    // Chat Groups & Messages
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [App\Http\Controllers\ChatGroupController::class, 'index'])
            ->middleware('throttle:study-get')
            ->name('index');

        Route::get('/{group}', [App\Http\Controllers\ChatGroupController::class, 'show'])
            ->middleware('throttle:study-get')
            ->name('show')
            ->where('group', '[0-9]+');

        Route::get('/{group}/messages', [App\Http\Controllers\ChatMessageController::class, 'fetch'])
            ->middleware('throttle:study-get')
            ->name('messages.fetch')
            ->where('group', '[0-9]+');

        Route::post('/{group}/messages', [App\Http\Controllers\ChatMessageController::class, 'store'])
            ->middleware(['throttle:study-post', 'throttle:chat-write'])
            ->name('messages.store')
            ->where('group', '[0-9]+');

        // Message actions
        Route::patch('/messages/{message}', [App\Http\Controllers\ChatMessageController::class, 'update'])
            ->middleware(['throttle:study-post', 'throttle:chat-write'])
            ->name('messages.update')
            ->where('message', '[0-9]+');

        Route::delete('/messages/{message}', [App\Http\Controllers\ChatMessageController::class, 'destroy'])
            ->middleware(['throttle:study-post', 'throttle:chat-write'])
            ->name('messages.destroy')
            ->where('message', '[0-9]+');

        Route::post('/messages/{message}/report', [App\Http\Controllers\ChatMessageController::class, 'report'])
            ->middleware(['throttle:study-post', 'throttle:chat-write'])
            ->name('messages.report')
            ->where('message', '[0-9]+');

        Route::post('/messages/{message}/forward', [App\Http\Controllers\ChatMessageController::class, 'forward'])
            ->middleware(['throttle:study-post', 'throttle:chat-write'])
            ->name('messages.forward')
            ->where('message', '[0-9]+');

        // User xin vào nhóm chat
        Route::post('/{group}/join', [App\Http\Controllers\ChatGroupController::class, 'requestToJoin'])
            ->middleware('throttle:study-post')
            ->name('groups.request-join')
            ->where('group', '[0-9]+');
    });

    // Minna no Nihongo - action cần đăng nhập
    Route::prefix('minna')->name('minna.')->group(function () {
        Route::post('/bai-{number}/quiz-nang-cao', [App\Http\Controllers\MinnaController::class, 'submitAdvancedQuiz'])
            ->middleware('throttle:study-post')
            ->name('quiz.advanced.submit');

        Route::post('/bai-{number}/hoan-thanh', [App\Http\Controllers\MinnaController::class, 'complete'])
            ->middleware('throttle:study-post')
            ->name('complete');

        Route::post('/bai-{number}/phan-{section}/hoan-thanh', [App\Http\Controllers\MinnaController::class, 'completeSection'])
            ->middleware('throttle:study-post')
            ->name('section.complete');

        Route::post('/bai-{number}/quiz', [App\Http\Controllers\MinnaController::class, 'submitQuiz'])
            ->middleware('throttle:study-post')
            ->name('quiz.submit');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.route.permission', 'throttle:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('logo-settings', [App\Http\Controllers\Admin\LogoSettingController::class, 'index'])->name('logo-settings.index');
    Route::post('logo-settings', [App\Http\Controllers\Admin\LogoSettingController::class, 'store'])->name('logo-settings.store');
    Route::put('logo-settings', [App\Http\Controllers\Admin\LogoSettingController::class, 'update'])->name('logo-settings.update');
    Route::delete('logo-settings', [App\Http\Controllers\Admin\LogoSettingController::class, 'destroy'])->name('logo-settings.destroy');
    Route::get('security', [App\Http\Controllers\Admin\SecurityController::class, 'index'])->name('security.index');
    Route::post('security', [App\Http\Controllers\Admin\SecurityController::class, 'update'])->name('security.update');
    Route::get('system-health', [App\Http\Controllers\Admin\SystemHealthController::class, 'index'])->name('system-health.index');
    Route::get('system-logs', [App\Http\Controllers\Admin\SystemLogController::class, 'index'])->name('system-logs.index');
    Route::get('audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('content-reports', [App\Http\Controllers\Admin\ContentErrorReportController::class, 'index'])->name('content-reports.index');
    Route::get('content-reports/{contentReport}', [App\Http\Controllers\Admin\ContentErrorReportController::class, 'show'])->name('content-reports.show');
    Route::patch('content-reports/{contentReport}', [App\Http\Controllers\Admin\ContentErrorReportController::class, 'update'])->name('content-reports.update');
    Route::get('content-ops', [App\Http\Controllers\Admin\ContentOperationsController::class, 'index'])->name('content-ops.index');
    Route::patch('content-ops/{type}/{id}/status', [App\Http\Controllers\Admin\ContentOperationsController::class, 'updateStatus'])->name('content-ops.status');
    Route::get('content-ops/{type}/{id}/preview', [App\Http\Controllers\Admin\ContentOperationsController::class, 'preview'])->name('content-ops.preview');
    Route::post('content-ops/{type}/{id}/publish-requests', [App\Http\Controllers\Admin\ContentOperationsController::class, 'requestPublish'])->name('content-ops.publish-requests.store');
    Route::post('content-ops/publish-requests/{publishRequest}/approve', [App\Http\Controllers\Admin\ContentOperationsController::class, 'approveRequest'])->name('content-ops.publish-requests.approve');
    Route::post('content-ops/publish-requests/{publishRequest}/reject', [App\Http\Controllers\Admin\ContentOperationsController::class, 'rejectRequest'])->name('content-ops.publish-requests.reject');
    Route::get('content-ops/{type}/{id}/versions', [App\Http\Controllers\Admin\ContentOperationsController::class, 'versions'])->name('content-ops.versions');
    Route::post('content-ops/versions/{version}/restore', [App\Http\Controllers\Admin\ContentOperationsController::class, 'restore'])->name('content-ops.restore');
    Route::get('audio', [App\Http\Controllers\Admin\AudioManagerController::class, 'index'])->name('audio.index');
    Route::post('audio/generate', [App\Http\Controllers\Admin\AudioManagerController::class, 'generate'])->name('audio.generate');
    Route::post('audio/bulk-generate', [App\Http\Controllers\Admin\AudioManagerController::class, 'bulkGenerate'])->name('audio.bulk-generate');
    Route::delete('audio/{audio}', [App\Http\Controllers\Admin\AudioManagerController::class, 'destroy'])->name('audio.destroy');
    Route::get('learning-analytics', [App\Http\Controllers\Admin\LearningAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('support-moderation', [App\Http\Controllers\Admin\SupportModerationController::class, 'index'])->name('support-moderation.index');
    Route::post('support-moderation/reports/{report}/dismiss', [App\Http\Controllers\Admin\SupportModerationController::class, 'dismissReport'])->name('support-moderation.reports.dismiss');
    Route::post('support-moderation/reports/{report}/remove-message', [App\Http\Controllers\Admin\SupportModerationController::class, 'removeReportedMessage'])->name('support-moderation.reports.remove-message');
    Route::get('growth', [App\Http\Controllers\Admin\GrowthToolsController::class, 'index'])->name('growth.index');
    Route::get('growth/create', [App\Http\Controllers\Admin\GrowthToolsController::class, 'create'])->name('growth.create');
    Route::post('growth', [App\Http\Controllers\Admin\GrowthToolsController::class, 'store'])->name('growth.store');
    Route::post('growth/{campaign}/send', [App\Http\Controllers\Admin\GrowthToolsController::class, 'send'])->name('growth.send');
    Route::post('users/{user}/lock', [App\Http\Controllers\Admin\UserController::class, 'lock'])->name('users.lock');
    Route::post('users/{user}/unlock', [App\Http\Controllers\Admin\UserController::class, 'unlock'])->name('users.unlock');
    Route::put('users/{user}/admin-roles', [App\Http\Controllers\Admin\UserController::class, 'updateAdminRoles'])->name('users.admin-roles.update');
    Route::resource('admin-roles', App\Http\Controllers\Admin\AdminRoleController::class)->except(['show']);
    Route::get('notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('notifications/read-all', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::prefix('inbox')->name('inbox.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DirectInboxController::class, 'index'])->name('index');
        Route::get('/unread-count', [App\Http\Controllers\Admin\DirectInboxController::class, 'unreadCount'])->name('unread-count');
        Route::get('/{user}', [App\Http\Controllers\Admin\DirectInboxController::class, 'show'])
            ->name('show')
            ->where('user', '[0-9]+');
        Route::get('/conversations/{conversation}/messages', [App\Http\Controllers\Admin\DirectInboxController::class, 'fetch'])
            ->name('messages.fetch')
            ->where('conversation', '[0-9]+');
        Route::post('/{user}/messages', [App\Http\Controllers\Admin\DirectInboxController::class, 'store'])
            ->middleware('throttle:chat-write')
            ->name('messages.store')
            ->where('user', '[0-9]+');
    });
    Route::resource('alphabets', App\Http\Controllers\Admin\AlphabetController::class);
    Route::resource('kanjis', App\Http\Controllers\Admin\KanjiController::class);
    Route::resource('minna', App\Http\Controllers\Admin\MinnaController::class);
    Route::post('minna/{minna}/add-sections', [App\Http\Controllers\Admin\MinnaController::class, 'addSections'])->name('minna.add-sections');
    Route::get('minna-sections/{minnaSection}/edit', [App\Http\Controllers\Admin\MinnaSectionController::class, 'edit'])->name('minna-section.edit');
    Route::put('minna-sections/{minnaSection}', [App\Http\Controllers\Admin\MinnaSectionController::class, 'update'])->name('minna-section.update');
    Route::post('course-data/{course_datum}/duplicate', [App\Http\Controllers\Admin\CourseDataController::class, 'duplicate'])
        ->name('course-data.duplicate');
    Route::resource('course-data', App\Http\Controllers\Admin\CourseDataController::class);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['create', 'store']);

    // Chat groups (admin quản lý)
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('groups', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'index'])
            ->name('groups.index');
        Route::get('groups/create', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'create'])
            ->name('groups.create');
        Route::post('groups', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'store'])
            ->name('groups.store');

        Route::get('groups/{group}', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'show'])
            ->name('groups.show');
        Route::get('groups/{group}/edit', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'edit'])
            ->name('groups.edit');
        Route::put('groups/{group}', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'update'])
            ->name('groups.update');
        Route::delete('groups/{group}', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'destroy'])
            ->name('groups.destroy');

        Route::post('join-requests/{joinRequest}/approve', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'approveJoin'])
            ->name('groups.join-requests.approve');
        Route::post('join-requests/{joinRequest}/decline', [App\Http\Controllers\Admin\ChatGroupAdminController::class, 'declineJoin'])
            ->name('groups.join-requests.decline');
    });
});
