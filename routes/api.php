<?php

use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\Api\FlashcardApiController;
use App\Http\Controllers\Api\MinnaLessonController;
use App\Http\Controllers\Api\PronunciationController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\UserLearningController;
use App\Http\Controllers\AiTutorController;
use App\Http\Controllers\FavoriteItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'japanese-study-api',
    ]);
});

Route::get('/dictionary/lookup', [DictionaryController::class, 'lookup']);
Route::get('/pronunciation/resolve', [PronunciationController::class, 'resolve']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/google', [AuthController::class, 'loginWithGoogle']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::prefix('learning')->group(function () {
        Route::get('/dashboard', [UserLearningController::class, 'dashboard']);
        Route::get('/progress', [UserLearningController::class, 'progress']);
        Route::get('/mistakes', [UserLearningController::class, 'mistakes']);
        Route::get('/weekly-goal', [UserLearningController::class, 'weeklyGoal']);
        Route::get('/topics', [UserLearningController::class, 'practicalTopics']);
        Route::get('/topics/{slug}', [UserLearningController::class, 'practicalTopic']);
        Route::get('/statistics', [UserLearningController::class, 'statistics']);
        Route::get('/kanji/levels', [UserLearningController::class, 'kanjiLevels']);
        Route::get('/kanji/{level}', [UserLearningController::class, 'kanjiList']);
        Route::get('/vocabulary/lessons', [UserLearningController::class, 'vocabularyLessons']);
        Route::get('/vocabulary/{number}', [UserLearningController::class, 'vocabularyByLesson'])
            ->whereNumber('number');
        Route::get('/courses', [UserLearningController::class, 'courseLevels']);
        Route::get('/courses/{level}', [UserLearningController::class, 'courseSections']);
        Route::get('/courses/{level}/{sectionType}', [UserLearningController::class, 'courseSectionItems']);
        Route::get('/courses/{level}/{sectionType}/{itemKey}', [UserLearningController::class, 'courseSectionItemDetail']);
        Route::get('/search', [UserLearningController::class, 'search']);
    });

    Route::prefix('minna')->group(function () {
        Route::get('/lessons', [MinnaLessonController::class, 'index']);
        Route::get('/lessons/{number}', [MinnaLessonController::class, 'show'])
            ->whereNumber('number');
        Route::post('/lessons/{number}/ai-tutor', AiTutorController::class)
            ->middleware('throttle:study-post')
            ->whereNumber('number');
    });

    Route::prefix('flashcards')->group(function () {
        Route::post('/study', [FlashcardApiController::class, 'study']);
        Route::get('/favorites', [FlashcardApiController::class, 'favorites']);
        Route::post('/review', [FlashcardApiController::class, 'review']);
    });

    Route::post('/favorites', [FavoriteItemController::class, 'store']);
    Route::delete('/favorites/{favoriteItem}', [FavoriteItemController::class, 'destroy'])
        ->whereNumber('favoriteItem');

    Route::prefix('social')->group(function () {
        Route::get('/groups', [SocialController::class, 'groups']);
        Route::post('/groups/{group}/join', [SocialController::class, 'joinGroup'])
            ->whereNumber('group');
        Route::get('/groups/{group}/messages', [SocialController::class, 'groupMessages'])
            ->whereNumber('group');
        Route::post('/groups/{group}/messages', [SocialController::class, 'sendGroupMessage'])
            ->middleware('throttle:chat-write')
            ->whereNumber('group');

        Route::get('/inbox/conversations', [SocialController::class, 'conversations']);
        Route::get('/inbox/conversations/{conversation}/messages', [SocialController::class, 'conversationMessages'])
            ->whereNumber('conversation');
        Route::post('/inbox/conversations/{conversation}/messages', [SocialController::class, 'sendConversationMessage'])
            ->middleware('throttle:chat-write')
            ->whereNumber('conversation');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminApiController::class, 'dashboard']);
        Route::get('/users', [AdminApiController::class, 'users']);
        Route::post('/users/{user}/lock', [AdminApiController::class, 'lockUser'])
            ->whereNumber('user');
        Route::post('/users/{user}/unlock', [AdminApiController::class, 'unlockUser'])
            ->whereNumber('user');

        Route::get('/notifications', [AdminApiController::class, 'notifications']);
        Route::post('/notifications/{notification}/read', [AdminApiController::class, 'markNotificationRead'])
            ->whereNumber('notification');
        Route::post('/notifications/read-all', [AdminApiController::class, 'markAllNotificationsRead']);

        Route::get('/moderation', [AdminApiController::class, 'moderation']);
        Route::post('/join-requests/{joinRequest}/approve', [AdminApiController::class, 'approveJoinRequest'])
            ->whereNumber('joinRequest');
        Route::post('/join-requests/{joinRequest}/decline', [AdminApiController::class, 'declineJoinRequest'])
            ->whereNumber('joinRequest');
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user(),
    ]);
});
