<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Article;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GradeOneController extends Controller
{
    public function index($database): JsonResponse
    {
        try {
            // التحقق من صحة اسم قاعدة البيانات
            if (!in_array($database, ['jo', 'sa', 'eg', 'ps'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid database name'
                ], 400);
            }

            // الحصول على البيانات
            $lessons = SchoolClass::on($database)->get();
            $classes = SchoolClass::on($database)->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'lessons' => $lessons,
                    'classes' => $classes
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($database, $id): JsonResponse
    {
        try {
            if (!in_array($database, ['jo', 'sa', 'eg', 'ps'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid database name'
                ], 400);
            }

            $class = SchoolClass::on($database)->findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $class
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Class not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showSubject($database, $subject): JsonResponse
    {
        try {
            if (!in_array($database, ['jo', 'sa', 'eg', 'ps'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid database name'
                ], 400);
            }

            $subject = Subject::on($database)
                ->with(['semesters', 'articles'])
                ->findOrFail($subject);
            
            return response()->json([
                'status' => 'success',
                'data' => $subject
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subject not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function subjectArticles($database, $subject, $semester, $category): JsonResponse
    {
        try {
            if (!in_array($database, ['jo', 'sa', 'eg', 'ps'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid database name'
                ], 400);
            }

            $articles = Article::on($database)
                ->where('subject_id', $subject)
                ->where('semester_id', $semester)
                ->whereHas('files', function ($query) use ($category) {
                    $query->where('file_category', $category);
                })
                ->with(['files' => function ($query) use ($category) {
                    $query->where('file_category', $category);
                }])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $articles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the articles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showArticle($database, $article): JsonResponse
    {
        try {
            if (!in_array($database, ['jo', 'sa', 'eg', 'ps'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid database name'
                ], 400);
            }

            $article = Article::on($database)
                ->with(['subject', 'semester', 'files', 'keywords'])
                ->findOrFail($article);

            // زيادة عداد الزيارات
            $article->increment('visit_count');

            return response()->json([
                'status' => 'success',
                'data' => $article
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Article not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the article',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile($database, $id): JsonResponse
    {
        try {
            if (!in_array($database, ['jo', 'sa', 'eg', 'ps'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid database name'
                ], 400);
            }

            $file = File::on($database)->findOrFail($id);
            $file->increment('download_count');

            $filePath = storage_path('app/public/' . $file->file_path);
            if (!file_exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'file_path' => asset('storage/' . $file->file_path),
                    'file_name' => $file->file_name,
                    'download_count' => $file->download_count
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'File not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing the file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
