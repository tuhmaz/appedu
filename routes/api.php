<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\GradeOneController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Api\NewsController;
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

// Authentication Routes (Main Database Only)
Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('api.auth.logout');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum')->name('api.auth.user');

// Public Content Routes
Route::middleware(['api', 'throttle:api'])->group(function () {
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

        // Frontend News Routes (Public)
        Route::prefix('news')->group(function () {
            Route::get('/', [FrontendNewsController::class, 'index'])->name('api.frontend.news.index');
            Route::get('/{id}', [FrontendNewsController::class, 'show'])->name('api.frontend.news.show');
            Route::get('/category/{categorySlug}', [FrontendNewsController::class, 'category'])->name('api.frontend.news.category');
        });

        // Keywords & Categories
        Route::get('/keywords', [KeywordController::class, 'index'])->name('api.frontend.keywords.index');
        Route::get('/keywords/{keywords}', [KeywordController::class, 'indexByKeyword'])->name('api.frontend.keywords.by-keyword');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('api.frontend.categories.show');

        // File Downloads
        Route::get('/files/download/{id}', [FileController::class, 'downloadFile'])->name('api.frontend.files.download');
        Route::get('/download/{file}', [FileController::class, 'showDownloadPage'])->name('api.frontend.files.show-download');
        Route::get('/download-wait/{file}', [FileController::class, 'processDownload'])->name('api.frontend.files.process-download');
    });
});

// Protected Routes (Admin Panel)
Route::middleware(['auth:sanctum'])->group(function () {
    // Admin News Management
    Route::prefix('admin')->group(function () {
        Route::apiResource('news', NewsController::class)->names([
            'index' => 'api.admin.news.index',
            'store' => 'api.admin.news.store',
            'show' => 'api.admin.news.show',
            'update' => 'api.admin.news.update',
            'destroy' => 'api.admin.news.destroy',
        ]);
    });

    // Dashboard Resources
    Route::apiResource('school-classes', SchoolClassController::class)->names([
        'index' => 'api.admin.school-classes.index',
        'store' => 'api.admin.school-classes.store',
        'show' => 'api.admin.school-classes.show',
        'update' => 'api.admin.school-classes.update',
        'destroy' => 'api.admin.school-classes.destroy',
    ]);

    Route::apiResource('subjects', SubjectController::class)->names([
        'index' => 'api.admin.subjects.index',
        'store' => 'api.admin.subjects.store',
        'show' => 'api.admin.subjects.show',
        'update' => 'api.admin.subjects.update',
        'destroy' => 'api.admin.subjects.destroy',
    ]);

    Route::apiResource('semesters', SemesterController::class)->names([
        'index' => 'api.admin.semesters.index',
        'store' => 'api.admin.semesters.store',
        'show' => 'api.admin.semesters.show',
        'update' => 'api.admin.semesters.update',
        'destroy' => 'api.admin.semesters.destroy',
    ]);

    Route::apiResource('articles', ArticleController::class)->names([
        'index' => 'api.admin.articles.index',
        'store' => 'api.admin.articles.store',
        'show' => 'api.admin.articles.show',
        'update' => 'api.admin.articles.update',
        'destroy' => 'api.admin.articles.destroy',
    ]);

    Route::apiResource('files', FileController::class)->names([
        'index' => 'api.admin.files.index',
        'store' => 'api.admin.files.store',
        'show' => 'api.admin.files.show',
        'update' => 'api.admin.files.update',
        'destroy' => 'api.admin.files.destroy',
    ]);

    Route::apiResource('categories', CategoryController::class)->names([
        'index' => 'api.admin.categories.index',
        'store' => 'api.admin.categories.store',
        'show' => 'api.admin.categories.show',
        'update' => 'api.admin.categories.update',
        'destroy' => 'api.admin.categories.destroy',
    ]);

    // Calendar
    Route::prefix('calendar')->group(function () {
        Route::get('/{month?}/{year?}', [CalendarController::class, 'calendar'])->name('api.admin.calendar.show');
        Route::post('/event', [CalendarController::class, 'store'])->name('api.admin.calendar.store');
        Route::put('/event/{id}', [CalendarController::class, 'update'])->name('api.admin.calendar.update');
        Route::delete('/event/{id}', [CalendarController::class, 'destroy'])->name('api.admin.calendar.destroy');
    });

    // Messages
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('api.messages.index');
        Route::post('/', [MessageController::class, 'send'])->name('api.messages.send');
        Route::get('/sent', [MessageController::class, 'sent'])->name('api.messages.sent');
        Route::get('/received', [MessageController::class, 'received'])->name('api.messages.received');
        Route::get('/important', [MessageController::class, 'important'])->name('api.messages.important');
        Route::get('/drafts', [MessageController::class, 'drafts'])->name('api.messages.drafts');
        Route::get('/trash', [MessageController::class, 'trash'])->name('api.messages.trash');
        Route::delete('/trash', [MessageController::class, 'deleteTrash'])->name('api.messages.delete-trash');
        Route::get('/{id}', [MessageController::class, 'show'])->name('api.messages.show');
        Route::post('/{id}/reply', [MessageController::class, 'reply'])->name('api.messages.reply');
        Route::post('/{id}/mark-as-read', [MessageController::class, 'markAsRead'])->name('api.messages.mark-as-read');
        Route::post('/{id}/toggle-important', [MessageController::class, 'toggleImportant'])->name('api.messages.toggle-important');
        Route::delete('/{id}', [MessageController::class, 'delete'])->name('api.messages.delete');
    });

    // Analytics & Performance
    Route::prefix('analytics')->group(function () {
        Route::get('/visitors', [AnalyticsController::class, 'visitors'])->name('api.admin.analytics.visitors');
        Route::get('/performance', [PerformanceController::class, 'index'])->name('api.admin.performance.index');
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('api.admin.notifications.index');
        Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('api.admin.notifications.mark-read');
    });

    // Comments & Reactions
    Route::post('/comments', [CommentController::class, 'store'])->name('api.comments.store');
    Route::post('/reactions', [ReactionController::class, 'store'])->name('api.reactions.store');

    // Filters
    Route::prefix('filter')->group(function () {
        Route::get('/files', [FilterController::class, 'index'])->name('api.filter.files');
        Route::get('/subjects/{classId}', [FilterController::class, 'getSubjectsByClass'])->name('api.filter.subjects');
        Route::get('/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject'])->name('api.filter.semesters');
        Route::get('/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester'])->name('api.filter.file-types');
    });
});

// Additional Subject Routes
Route::get('/subjects/by-grade/{grade_level}', [SubjectController::class, 'indexByGrade'])->name('api.subjects.by-grade');
Route::get('/classes-by-country/{country}', [SubjectController::class, 'getClassesByCountry'])->name('api.classes-by-country');
