<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\KeywordController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\GradeOneController;
use App\Http\Controllers\Api\FrontendNewsController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\UserController;

// تطبيق middleware الحماية على جميع مسارات API
Route::middleware(['auth:sanctum'])->group(function () {
    // Open Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::get('/logout', [AuthController::class, 'logout']);
    });
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // Setting Database Connection
    Route::post('/set-database', [HomeController::class, 'setDatabase'])->name('api.setDatabase');

    // General Upload Routes
    Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('api.upload.image');
    Route::post('/upload-file', [ImageUploadController::class, 'uploadFile'])->name('api.upload.file');

    // Authenticated and Dashboard Routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::prefix('dashboard')->group(function () {
            // Dashboard Index
            Route::get('/', [DashboardController::class, 'index'])->name('api.dashboard.index');

            // School Classes API Routes
            Route::apiResource('/classes', SchoolClassController::class)->middleware(['can:manage classes'])->names([
                'index' => 'api.classes.index',
                'store' => 'api.classes.store',
                'show' => 'api.classes.show',
                'update' => 'api.classes.update',
                'destroy' => 'api.classes.destroy',
            ]);

            // Subject API Routes
            Route::apiResource('/subjects', SubjectController::class)->middleware(['can:manage subjects'])->names([
                'index' => 'api.subjects.index',
                'store' => 'api.subjects.store',
                'show' => 'api.subjects.show',
                'update' => 'api.subjects.update',
                'destroy' => 'api.subjects.destroy',
            ]);

            // Semester API Routes
            Route::apiResource('/semesters', SemesterController::class)->names([
                'index' => 'api.semesters.index',
                'store' => 'api.semesters.store',
                'show' => 'api.semesters.show',
                'update' => 'api.semesters.update',
                'destroy' => 'api.semesters.destroy',
            ]);

            // Articles API Routes
            Route::apiResource('/articles', ArticleController::class)->names([
                'index' => 'api.articles.index',
                'store' => 'api.articles.store',
                'show' => 'api.articles.show',
                'update' => 'api.articles.update',
                'destroy' => 'api.articles.destroy',
            ]);
            Route::get('/class/{grade_level}', [ArticleController::class, 'indexByClass'])->name('api.articles.forClass');

            // File API Routes
            Route::apiResource('/files', FileController::class)->names([
                'index' => 'api.files.index',
                'store' => 'api.files.store',
                'show' => 'api.files.show',
                'update' => 'api.files.update',
                'destroy' => 'api.files.destroy',
            ]);
            Route::get('/files/{id}/download', [FileController::class, 'downloadFile'])->name('api.files.download');

            // Categories API Routes
            Route::apiResource('/categories', CategoryController::class)->names([
                'index' => 'api.categories.index',
                'store' => 'api.categories.store',
                'show' => 'api.categories.show',
                'update' => 'api.categories.update',
                'destroy' => 'api.categories.destroy',
            ]);

            // News API Routes
            Route::apiResource('/news', NewsController::class)->names([
                'index' => 'api.news.index',
                'store' => 'api.news.store',
                'show' => 'api.news.show',
                'update' => 'api.news.update',
                'destroy' => 'api.news.destroy',
            ]);

            // Messages API Routes
            Route::apiResource('/messages', MessageController::class)->names([
                'index' => 'api.messages.index',
                'store' => 'api.messages.store',
                'show' => 'api.messages.show',
                'update' => 'api.messages.update',
                'destroy' => 'api.messages.destroy',
            ]);

            // Notifications API Routes
            Route::apiResource('/notifications', NotificationController::class)->names([
                'index' => 'api.notifications.index',
                'store' => 'api.notifications.store',
                'show' => 'api.notifications.show',
                'update' => 'api.notifications.update',
                'destroy' => 'api.notifications.destroy',
            ]);

            // Calendar API Routes
            Route::apiResource('/calendar', CalendarController::class)->names([
                'index' => 'api.calendar.index',
                'store' => 'api.calendar.store',
                'show' => 'api.calendar.show',
                'update' => 'api.calendar.update',
                'destroy' => 'api.calendar.destroy',
            ]);

            // Roles, Permissions, Reactions Routes
            Route::apiResource('/roles', RoleController::class)->names([
              'index' => 'api.roles.index',
              'store' => 'api.roles.store',
              'show' => 'api.roles.show',
              'update' => 'api.roles.update',
              'destroy' => 'api.roles.destroy',
          ]);

          Route::apiResource('/permissions', PermissionController::class)->names([
            'index' => 'api.permissions.index',
            'store' => 'api.permissions.store',
            'show' => 'api.permissions.show',
            'update' => 'api.permissions.update',
            'destroy' => 'api.permissions.destroy',
        ]);

        Route::apiResource('/reactions', ReactionController::class)->names([
          'index' => 'api.reactions.index',
          'store' => 'api.reactions.store',
          'show' => 'api.reactions.show',
          'update' => 'api.reactions.update',
          'destroy' => 'api.reactions.destroy',
      ]);


            // Comments Route
            Route::post('/comments', [CommentController::class, 'store'])->name('api.comments.store');
        });
    });

    // User routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class)->names([
            'index' => 'api.users.index',
            'store' => 'api.users.store',
            'show' => 'api.users.show',
            'update' => 'api.users.update',
            'destroy' => 'api.users.destroy'
        ]);
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile', [UserController::class, 'updateProfile']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        
        // Avatar routes
        Route::post('users/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('users/avatar', [UserController::class, 'deleteAvatar']);
        Route::get('users/{id}/avatar', [UserController::class, 'getAvatar']);
    });

    // Home Controller
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/about', [HomeController::class, 'about']);
    Route::get('/contact', [HomeController::class, 'contact']);

    // Filter Routes
    Route::get('/filter-files', [FilterController::class, 'index']);
    Route::get('/api/subjects/{classId}', [FilterController::class, 'getSubjectsByClass']);
    Route::get('/api/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject']);
    Route::get('/api/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester']);

    // Database-Specific Routes (by prefix)
    Route::prefix('{database}')->group(function () {
        Route::get('/news', [FrontendNewsController::class, 'index']);
        Route::get('/news/{id}', [FrontendNewsController::class, 'show']);
        Route::get('/news/category/{category}', [FrontendNewsController::class, 'category']);

        Route::prefix('lesson')->group(function () {
            Route::get('/', [GradeOneController::class, 'index'])->name('api.lesson.index');
            Route::get('/{id}', [GradeOneController::class, 'show']);
            Route::get('subjects/{subject}', [GradeOneController::class, 'showSubject']);
            Route::get('subjects/{subject}/articles/{semester}/{category}', [GradeOneController::class, 'subjectArticles']);
            Route::get('/articles/{article}', [GradeOneController::class, 'showArticle']);
            Route::get('files/download/{id}', [GradeOneController::class, 'downloadFile']);
        });
    });

    // Keywords Routes
    Route::get('/keywords', [KeywordController::class, 'index']);
    Route::get('/keywords/{keyword}', [KeywordController::class, 'indexByKeyword']);
});

// منع الوصول المباشر لأي مسار API غير معرف
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'غير مصرح بالوصول'
    ], 403);
});
