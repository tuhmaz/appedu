<?php

use Illuminate\Support\Facades\Route;
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


Route::group(['middleware' => 'switch_database'], function () {


  Route::resource('school_classes', SchoolClassController::class);

});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/set-database', [HomeController::class, 'setDatabase'])->name('setDatabase');


Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('upload.image');
Route::get('/lang/{locale}', [LanguageController::class, 'swap'])->name('dashboard.lang-swap');




// Lesson routes
Route::prefix('lesson')->group(function () {
    Route::get('/', [GradeOneController::class, 'index'])->name('class.index');

    Route::get('{id}', [GradeOneController::class, 'show'])->name('frontend.class.show');
    Route::get('subjects/{subject}', [GradeOneController::class, 'showSubject'])->name('frontend.subjects.show');
    Route::get('subjects/{subject}/articles/{semester}/{category}', [GradeOneController::class, 'subjectArticles'])->name('frontend.subject.articles');
    Route::get('articles/{article}', [GradeOneController::class, 'showArticle'])->name('frontend.articles.show');
    Route::get('files/download/{id}', [FileController::class, 'downloadFile'])->name('files.download');



});


Route::get('/keywords', [KeywordController::class, 'index'])->name('frontend.keywords.index');





Route::get('/keywords/{keywords}', [ArticleController::class, 'indexByKeyword'])->name('articles.indexByKeyword');

Route::get('/news', [NewsController::class, 'index'])->name('frontend.news.index');
 Route::get('/news/{id}', [NewsController::class, 'show'])->name('frontend.news.show');
Route::get('/news/category/{category}', [NewsController::class, 'category'])->name('frontend.news.category');
//Route::get('/news/{news}', [NewsController::class, 'show'])->name('frontend.news.show');

// Filter routes
Route::get('/filter-files', [FilterController::class, 'index'])->name('files.filter');
Route::get('/api/subjects/{classId}', [FilterController::class, 'getSubjectsByClass']);
Route::get('/api/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject']);
Route::get('/api/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester']);



// Dashboard routes (protected by authentication)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('dashboard')->group(function () {
    // Main Page Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

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
    Route::put('articles/{id}/edit', [ArticleController::class, 'update'])->name('articles.update');

    Route::get('articles/class/{grade_level}', [ArticleController::class, 'indexByClass'])->name('articles.forClass')->middleware('can:manage articles');
    Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show')->middleware('can:manage articles');




    // Files routes
    Route::resource('files', FileController::class);

    // News routes
    Route::resource('news', NewsController::class)->middleware(['can:manage news']);


    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('can:manage settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('can:manage settings');

    // Error page route
    Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('dashboard.pages-misc-error');

    // Role & Permission Management routes
    Route::resource('roles', RoleController::class)->middleware(['can:manage roles']);
    Route::resource('permissions', PermissionController::class)->middleware(['can:manage permissions']);

    // Users routes
    Route::resource('users', UserController::class);
    Route::get('/users/{user}/permissions-roles', [UserController::class, 'permissions_roles'])->name('users.permissions_roles')->middleware('can:manage permissions');
    Route::put('/users/{user}/permissions-roles', [UserController::class, 'updatePermissionsRoles'])->name('users.updatePermissionsRoles')->middleware('can:manage permissions');

    // Notifications routes
    Route::resource('notifications', NotificationController::class)->only(['index', 'destroy']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/handle-actions', [NotificationController::class, 'handleActions'])->name('notifications.handleActions');
    Route::post('/notifications/{id}/delete', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::patch('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');


    // Comments & Reactions routes
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/reactions', [ReactionController::class, 'store'])->name('reactions.store');

    // حذف المسار الخاص بـ Trash messages
Route::prefix('messages')->group(function () {
  Route::get('compose', [MessageController::class, 'compose'])->name('messages.compose');
  Route::post('send', [MessageController::class, 'send'])->name('messages.send');
  Route::get('/', [MessageController::class, 'index'])->name('messages.index');
  Route::get('sent', [MessageController::class, 'sent'])->name('messages.sent');
  Route::get('received', [MessageController::class, 'received'])->name('messages.received');
  Route::get('important', [MessageController::class, 'important'])->name('messages.important');
  Route::get('drafts', [MessageController::class, 'drafts'])->name('messages.drafts');
  Route::get('trash', [MessageController::class, 'trash'])->name('messages.trash');
  Route::delete('trash', [MessageController::class, 'deleteTrash'])->name('messages.deleteTrash'); // تعديل هنا
  Route::delete('{id}', [MessageController::class, 'delete'])->name('messages.delete');
  Route::get('{id}', [MessageController::class, 'show'])->name('messages.show');
  Route::post('{id}/reply', [MessageController::class, 'reply'])->name('messages.reply');
  Route::post('/{id}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
  Route::post('{id}/toggle-important', [MessageController::class, 'toggleImportant'])->name('messages.toggleImportant');
});

});
