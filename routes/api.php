<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GradeOneController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Api\FrontendNewsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

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

// تكوين Rate Limiting
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// Public Routes
Route::middleware(['api', 'throttle:api'])->group(function () {
    // Authentication Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Public Content Routes
    Route::prefix('{database}')->group(function () {
        // Lessons
        Route::prefix('lesson')->group(function () {
            Route::get('/', [GradeOneController::class, 'index']);
            Route::get('/{id}', [GradeOneController::class, 'show']);
            Route::get('/subjects/{subject}', [GradeOneController::class, 'showSubject']);
            Route::get('/subjects/{subject}/articles/{semester}/{category}', [GradeOneController::class, 'subjectArticles']);
            Route::get('/articles/{article}', [GradeOneController::class, 'showArticle']);
            Route::get('/files/download/{id}', [GradeOneController::class, 'downloadFile']);
        });

        // News
        Route::prefix('news')->group(function () {
            Route::get('/', [FrontendNewsController::class, 'index']);
            Route::get('/{id}', [FrontendNewsController::class, 'show']);
            Route::get('/category/{category}', [FrontendNewsController::class, 'category']);
            Route::get('/filter', [FrontendNewsController::class, 'filterNewsByCategory']);
        });

        // Keywords & Categories
        Route::get('/keywords', [KeywordController::class, 'index']);
        Route::get('/keywords/{keywords}', [KeywordController::class, 'indexByKeyword'])->name('keywords.indexByKeyword');
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
    });

    // File Downloads
    Route::get('/files/download/{id}', [FileController::class, 'downloadFile']);
    Route::get('/download/{file}', [FileController::class, 'showDownloadPage']);
    Route::get('/download-wait/{file}', [FileController::class, 'processDownload']);
});

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User Management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Dashboard Resources
    Route::apiResource('school-classes', SchoolClassController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('semesters', SemesterController::class);
    Route::apiResource('articles', ArticleController::class);
    Route::apiResource('files', FileController::class);
    Route::apiResource('news', NewsController::class);
    Route::apiResource('categories', CategoryController::class);

    // Calendar
    Route::prefix('calendar')->group(function () {
        Route::get('/{month?}/{year?}', [CalendarController::class, 'calendar']);
        Route::post('/event', [CalendarController::class, 'store']);
        Route::put('/event/{id}', [CalendarController::class, 'update']);
        Route::delete('/event/{id}', [CalendarController::class, 'destroy']);
    });

    // Messages
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::post('/', [MessageController::class, 'send']);
        Route::get('/sent', [MessageController::class, 'sent']);
        Route::get('/received', [MessageController::class, 'received']);
        Route::get('/important', [MessageController::class, 'important']);
        Route::get('/drafts', [MessageController::class, 'drafts']);
        Route::get('/trash', [MessageController::class, 'trash']);
        Route::delete('/trash', [MessageController::class, 'deleteTrash']);
        Route::get('/{id}', [MessageController::class, 'show']);
        Route::post('/{id}/reply', [MessageController::class, 'reply']);
        Route::post('/{id}/mark-as-read', [MessageController::class, 'markAsRead']);
        Route::post('/{id}/toggle-important', [MessageController::class, 'toggleImportant']);
        Route::delete('/{id}', [MessageController::class, 'delete']);
    });

    // Analytics & Performance
    Route::prefix('analytics')->group(function () {
        Route::get('/visitors', [AnalyticsController::class, 'visitors']);
        Route::get('/performance', [PerformanceController::class, 'index']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead']);
    });

    // Comments & Reactions
    Route::post('/comments', [CommentController::class, 'store']);
    Route::post('/reactions', [ReactionController::class, 'store']);

    // Filters
    Route::prefix('filter')->group(function () {
        Route::get('/files', [FilterController::class, 'index']);
        Route::get('/subjects/{classId}', [FilterController::class, 'getSubjectsByClass']);
        Route::get('/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject']);
        Route::get('/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester']);
    });
});

// Additional Subject Routes
Route::get('/subjects/by-grade/{grade_level}', [SubjectController::class, 'indexByGrade']);
Route::get('/classes-by-country/{country}', [SubjectController::class, 'getClassesByCountry']);
