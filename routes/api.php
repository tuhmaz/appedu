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


//home controller
Route::post('/set-database', [HomeController::class, 'setDatabase']);
Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/contact', [HomeController::class, 'contact']);

Route::get('/calendar', [CalendarController::class, 'calendar']);
Route::post('/calendar/event', [CalendarController::class, 'store']);
Route::put('/calendar/event/{id}', [CalendarController::class, 'update']);
Route::delete('/calendar/event/{id}', [CalendarController::class, 'destroy']);


Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


Route::post('/comments', [CommentController::class, 'store']);


Route::get('/dashboard', [DashboardController::class, 'index']);


Route::get('/files', [FileController::class, 'index']);
Route::post('/files', [FileController::class, 'store']);
Route::get('/files/{id}', [FileController::class, 'show']);
Route::put('/files/{id}', [FileController::class, 'update']);
Route::delete('/files/{id}', [FileController::class, 'destroy']);
Route::get('/files/{id}/download', [FileController::class, 'downloadFile']);


Route::get('/filter-files', [FilterController::class, 'index']);
Route::get('/api/subjects/{classId}', [FilterController::class, 'getSubjectsByClass']);
Route::get('/api/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject']);
Route::get('/api/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester']);

Route::prefix('{database}')->group(function () {
  Route::get('/news', [FrontendNewsController::class, 'index']);
  Route::get('/news/{id}', [FrontendNewsController::class, 'show']);
  Route::get('/news/category/{category}', [FrontendNewsController::class, 'category']);

  Route::prefix('lesson')->group(function () {
      Route::get('/', [GradeOneController::class, 'index'])->name('lesson.index');
      Route::get('/{id}', [GradeOneController::class, 'show']);
      Route::get('subjects/{subject}', [GradeOneController::class, 'showSubject']);
      Route::get('subjects/{subject}/articles/{semester}/{category}', [GradeOneController::class, 'subjectArticles']);
      Route::get('/articles/{article}', [GradeOneController::class, 'showArticle']);
      Route::get('files/download/{id}', [GradeOneController::class, 'downloadFile']);
  });
});










Route::post('/upload-image', [ImageUploadController::class, 'upload']);
Route::post('/upload-file', [ImageUploadController::class, 'uploadFile']);


Route::post('/set-database', [KeywordController::class, 'setDatabase']);
Route::get('/keywords', [KeywordController::class, 'index']);
Route::get('/keywords/{keyword}', [KeywordController::class, 'indexByKeyword']);


Route::get('/messages', [MessageController::class, 'index']);
Route::post('/messages', [MessageController::class, 'store']);
Route::get('/messages/{id}', [MessageController::class, 'show']);
Route::post('/messages/{id}/reply', [MessageController::class, 'reply']);
Route::get('/messages/sent', [MessageController::class, 'sent']);
Route::patch('/messages/{id}/mark-as-read', [MessageController::class, 'markAsRead']);
Route::delete('/messages/{id}', [MessageController::class, 'delete']);
Route::post('/messages/delete-selected', [MessageController::class, 'deleteSelected']);
Route::patch('/messages/{id}/toggle-important', [MessageController::class, 'toggleImportant']);





Route::get('/notifications', [NotificationController::class, 'index']);
Route::patch('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
Route::patch('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
Route::delete('/notifications/delete-selected', [NotificationController::class, 'deleteSelected']);
Route::post('/notifications/handle-actions', [NotificationController::class, 'handleActions']);
Route::delete('/notifications/{id}', [NotificationController::class, 'delete']);


Route::get('/permissions', [PermissionController::class, 'index']);
Route::post('/permissions', [PermissionController::class, 'store']);
Route::get('/permissions/{id}', [PermissionController::class, 'show']);
Route::put('/permissions/{id}', [PermissionController::class, 'update']);
Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);


Route::post('/reactions', [ReactionController::class, 'store']);


Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'store']);
Route::get('/roles/{id}', [RoleController::class, 'show']);
Route::put('/roles/{id}', [RoleController::class, 'update']);
Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

Route::get('/semesters', [SemesterController::class, 'index']);
Route::post('/semesters', [SemesterController::class, 'store']);
Route::get('/semesters/{id}', [SemesterController::class, 'show']);
Route::put('/semesters/{id}', [SemesterController::class, 'update']);
Route::delete('/semesters/{id}', [SemesterController::class, 'destroy']);

Route::get('/subjects', [SubjectController::class, 'index']);
Route::post('/subjects', [SubjectController::class, 'store']);
Route::get('/subjects/{id}', [SubjectController::class, 'show']);
Route::put('/subjects/{id}', [SubjectController::class, 'update']);
Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);

// School Classes API Routes
//Route::apiResource('/classes', SchoolClassController::class);
Route::get('/school-classes', [SchoolClassController::class, 'index']);
Route::post('/school-classes', [SchoolClassController::class, 'store']);
Route::get('/school-classes/{id}', [SchoolClassController::class, 'show']);
Route::put('/school-classes/{id}', [SchoolClassController::class, 'update']);
Route::delete('/school-classes/{id}', [SchoolClassController::class, 'destroy']);
// Articles API Routes

Route::group(['prefix' => 'articles'], function () {
  Route::get('/', [ArticleController::class, 'index']); // List all articles
  Route::post('/', [ArticleController::class, 'store']); // Create a new article
  Route::get('/{id}', [ArticleController::class, 'show']); // Get a specific article by ID
  Route::put('/{id}', [ArticleController::class, 'update']); // Update an article
  Route::delete('/{id}', [ArticleController::class, 'destroy']); // Delete an article
  Route::get('/class/{grade_level}', [ArticleController::class, 'indexByClass']); // List articles by class (grade level)
});

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
