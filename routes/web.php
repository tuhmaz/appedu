<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FrontendNewsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\GradeOneController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PerformanceController;
use Illuminate\Support\Facades\Str;

// Authentication Routes
Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::group(['middleware' => 'switch_database'], function () {
  Route::resource('school_classes', SchoolClassController::class);
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/set-database', [HomeController::class, 'setDatabase'])->name('setDatabase');
Route::get('/lang/{locale}', [LanguageController::class, 'swap'])->name('dashboard.lang-swap');

// Terms of Service Route
Route::get('/terms-of-service', function () {
    $terms = File::get(resource_path('markdown/terms.md'));
    $terms = Str::markdown($terms);
    return view('terms', ['terms' => $terms]);
})->name('terms.show');

// Privacy Policy Route
Route::get('/privacy-policy', function () {
    $policy = File::get(resource_path('markdown/policy.md'));
    $policy = Str::markdown($policy);
    return view('policy', ['policy' => $policy]);
})->name('privacy-policy.show');

Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('upload.image');
Route::post('/upload-file', [ImageUploadController::class, 'uploadFile'])->name('upload.file');

// Dashboard routes (protected by authentication)

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Main Page Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    // API Documentation Route (protected)
    Route::middleware(['can:view-api-documentation'])->group(function () {
        Route::get('/api/documentation', function () {
            $documentation = 'default';
            $useAbsolutePath = config('l5-swagger.documentations.default.paths.use_absolute_path', true);
            $urlToDocs = route('l5-swagger.docs', ['jsonFile' => config('l5-swagger.documentations.default.paths.docs_json', 'api-docs.json')], $useAbsolutePath);

            return view('vendor.l5-swagger.index', [
                'documentation' => $documentation,
                'useAbsolutePath' => $useAbsolutePath,
                'urlToDocs' => $urlToDocs,
                'operationsSorter' => config('l5-swagger.defaults.ui.operations_sort', null),
                'configUrl' => config('l5-swagger.defaults.ui.config_url', null),
                'validatorUrl' => config('l5-swagger.defaults.ui.validator_url', null),
            ]);
        })->name('l5-swagger.api');

        // Add route for JSON documentation file
        Route::get('/docs/{jsonFile?}', function ($jsonFile = null) {
            $documentation = 'default';
            $jsonFile = $jsonFile ?: config('l5-swagger.documentations.default.paths.docs_json', 'api-docs.json');

            return Response::make(
                File::get(storage_path('api-docs/' . $jsonFile)),
                200,
                ['Content-Type' => 'application/json']
            );
        })->name('l5-swagger.docs');

        // Add route for OAuth2 callback
        Route::get('/oauth2-callback', function () {
            return view('vendor.l5-swagger.oauth2-redirect');
        })->name('l5-swagger.oauth2_callback');
    });

    //sitemap routes
    Route::get('/sitemap', [SitemapController::class, 'index'])->name('sitemap.index');
    Route::get('/sitemap/generate', [SitemapController::class, 'generate'])->name('sitemap.generate')->middleware('can:manage sitemap');
    Route::get('/sitemap/manage', [SitemapController::class, 'manageIndex'])->name('sitemap.manage')->middleware('can:manage sitemap');
    Route::post('/sitemap/update', [SitemapController::class, 'updateResourceInclusion'])->name('sitemap.updateResourceInclusion')->middleware('can:manage sitemap');
    Route::delete('/sitemap/delete/{type}/{database}', [SitemapController::class, 'delete'])->name('sitemap.delete')->middleware('can:manage sitemap');


    Route::get('sitemap/generate-articles', [SitemapController::class, 'generateArticlesSitemap'])->name('sitemap.generate.articles');
    Route::get('sitemap/generate-news', [SitemapController::class, 'generateNewsSitemap'])->name('sitemap.generate.news');
    Route::get('sitemap/generate-static', [SitemapController::class, 'generateStaticSitemap'])->name('sitemap.generate.static');


    //calendar
    Route::get('calendar/{month?}/{year?}', [CalendarController::class, 'calendar'])->name('calendar.index')->middleware('can:manage calendar');
    Route::post('calendar/event', [CalendarController::class, 'store'])->name('events.store')->middleware('can:manage calendar');
    Route::put('calendar/event/{event}', [CalendarController::class, 'update'])->name('events.update')->middleware('can:manage calendar');
    Route::delete('calendar/event/{event}', [CalendarController::class, 'destroy'])->name('events.destroy')->middleware('can:manage calendar');

    // Classes routes
    Route::resource('classes', SchoolClassController::class)->middleware(['can:manage classes']);

    // Subjects routes
    Route::resource('subjects', SubjectController::class)->middleware(['can:manage subjects']);
    Route::get('subjects/by-grade/{grade_level}', [SubjectController::class, 'indexByGrade'])->name('subjects.byGrade')->middleware('can:manage subjects');
    Route::get('/get-classes-by-country/{country}', [SubjectController::class, 'getClassesByCountry']);

    // Semesters routes
    Route::resource('semesters', SemesterController::class)->middleware(['can:manage semesters']);

    // Articles routes
    Route::resource('articles', ArticleController::class)->except(['show'])->middleware(['can:manage articles']);
     Route::get('articles/class/{grade_level}', [ArticleController::class, 'indexByClass'])->name('articles.forClass')->middleware('can:manage articles');
    Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show')->middleware('can:manage articles');


    // Files routes
    Route::resource('files', FileController::class);

    // News routes
     Route::resource('news', NewsController::class)->middleware(['can:manage news']);

    // Categories News routes
     Route::resource('categories', CategoryController::class)->middleware(['can:manage Categories']);

    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('can:manage settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('can:manage settings');

    // Error page route
    Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('dashboard.pages-misc-error');

    // Role & Permission Management routes
    Route::resource('roles', RoleController::class)->middleware(['can:manage roles']);
    Route::resource('permissions', PermissionController::class)->middleware(['can:manage permissions']);

    // Users routes
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('can:manage users');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::match(['put', 'post'], 'users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('web');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('can:manage users');

    // User Profile Photo Route
    Route::post('users/{user}/update-photo', [UserController::class, 'updateProfilePhoto'])
        ->name('users.update-photo')
        ->middleware(['web', 'auth']);

    // User Permissions & Roles Routes
    Route::get('users/{user}/permissions-roles', [UserController::class, 'permissions_roles'])
        ->name('users.permissions_roles')
        ->middleware('can:manage permissions');
    Route::put('users/{user}/permissions-roles', [UserController::class, 'updatePermissionsRoles'])
        ->name('users.updatePermissionsRoles')
        ->middleware('can:manage permissions');

    // Notifications routes
    Route::resource('notifications', NotificationController::class)->only(['index', 'destroy']);

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/handle-actions', [NotificationController::class, 'handleActions'])->name('notifications.handleActions');
    Route::post('/notifications/{id}/delete', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::patch('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Comments & Reactions routes
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/reactions', [ReactionController::class, 'store'])->name('reactions.store');

    // Performance Monitoring Routes
    Route::middleware(['cache.headers:public;max_age=60'])->group(function () {
        Route::get('/performance', [PerformanceController::class, 'dashboard'])->name('performance.dashboard');
        Route::get('/performance/metrics', [PerformanceController::class, 'metrics'])->name('performance.metrics');
    });

    // messages
  Route::prefix('messages')->group(function () {
    Route::get('compose', [MessageController::class, 'compose'])->name('messages.compose');
    Route::post('send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/', [MessageController::class, 'index'])->name('messages.index');
    Route::get('sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('received', [MessageController::class, 'received'])->name('messages.received');
    Route::get('important', [MessageController::class, 'important'])->name('messages.important');
    Route::get('drafts', [MessageController::class, 'drafts'])->name('messages.drafts');
    Route::get('trash', [MessageController::class, 'trash'])->name('messages.trash');
    Route::delete('trash', [MessageController::class, 'deleteTrash'])->name('messages.deleteTrash');
    Route::delete('{id}', [MessageController::class, 'delete'])->name('messages.delete');
    Route::get('{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('{id}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::post('/{id}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
    Route::post('{id}/toggle-important', [MessageController::class, 'toggleImportant'])->name('messages.toggleImportant');
   });

    // مسارات تتبع محاولات تسجيل الدخول
    Route::get('login-attempts', [App\Http\Controllers\Dashboard\LoginAttemptsController::class, 'index'])
        ->name('login-attempts.index')->middleware('can:manage settings');
    Route::get('login-attempts/security-report', [App\Http\Controllers\Dashboard\LoginAttemptsController::class, 'securityReport'])
        ->name('login-attempts.security-report')->middleware('can:manage settings');
    Route::get('login-attempts/export-report', [App\Http\Controllers\Dashboard\LoginAttemptsController::class, 'exportSecurityReport'])
        ->name('login-attempts.export-report')->middleware('can:manage settings');
    Route::get('login-attempts/{attempt}', [App\Http\Controllers\Dashboard\LoginAttemptsController::class, 'show'])
        ->name('login-attempts.show')->middleware('can:manage settings');
    Route::post('login-attempts/clear-old', [App\Http\Controllers\Dashboard\LoginAttemptsController::class, 'clearOld'])
        ->name('login-attempts.clear-old')->middleware('can:manage settings');
    Route::post('login-attempts/delete-selected', [App\Http\Controllers\Dashboard\LoginAttemptsController::class, 'deleteSelected'])
        ->name('login-attempts.delete-selected')->middleware('can:manage settings');

    // Analytics route
    Route::middleware(['auth'])->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'dashboard'])->name('analytics.dashboard')->middleware('can:manage settings');
    });
});

// Lesson for the Class
Route::prefix('{database}')->group(function () {
  Route::prefix('lesson')->group(function () {
   Route::get('/', [GradeOneController::class, 'index'])->name('class.index');
   Route::get('/{id}', [GradeOneController::class, 'show'])->name('frontend.class.show');
   Route::get('subjects/{subject}', [GradeOneController::class, 'showSubject'])->name('frontend.subjects.show');
   Route::get('subjects/{subject}/articles/{semester}/{category}', [GradeOneController::class, 'subjectArticles'])->name('frontend.subject.articles');
   Route::get('/articles/{article}', [GradeOneController::class, 'showArticle'])->name('frontend.articles.show');
   Route::get('files/download/{id}', [FileController::class, 'downloadFile'])->name('files.download');

  });

 // Keywords for the frontend
  Route::get('/keywords', [KeywordController::class, 'index'])->name('frontend.keywords.index');
  Route::get('/keywords/{keywords}', [KeywordController::class, 'indexByKeyword'])->name('keywords.indexByKeyword');


  //News for the frontend
  Route::get('/news', [FrontendNewsController::class, 'index'])->name('frontend.news.index');
  Route::get('/news/{id}', [FrontendNewsController::class, 'show'])->name('frontend.news.show');
  Route::get('/news/category/{category}', [FrontendNewsController::class, 'category'])->name('frontend.news.category');

  // Filter routes for news
 Route::get('news/filter', [FrontendNewsController::class, 'filterNewsByCategory'])->name('frontend.news.filter');

 Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('frontend.categories.show');

});

  // Filter routes
  Route::get('/filter-files', [FilterController::class, 'index'])->name('files.filter');
  Route::get('/api/subjects/{classId}', [FilterController::class, 'getSubjectsByClass']);
  Route::get('/api/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject']);
  Route::get('/api/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester']);
  // File downloaded waited

  Route::get('/download/{file}', [FileController::class, 'showDownloadPage'])->name('download.page');
  Route::get('/download-wait/{file}', [FileController::class, 'processDownload'])->name('download.wait');
