<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FrontendNewsController extends Controller
{
    private function getConnection(Request $request, $urlDatabase = null)
    {
        // استخدام قاعدة البيانات من URL أولاً، ثم من الطلب، ثم القيمة الافتراضية
        $database = $urlDatabase ?? $request->input('database', 'jo');
        
        // التحقق من صحة قاعدة البيانات
        if (!in_array($database, ['jo', 'sa', 'eg', 'ps'])) {
            $database = 'jo';
        }
        
        return $database;
    }

    public function index(Request $request, $database)
    {
        try {
            $database = $this->getConnection($request, $database);
            
            // استخدام Cache للفئات
            $categories = Cache::remember("categories_{$database}", 3600, function () use ($database) {
                return Category::on($database)
                    ->select('id', 'name', 'slug')
                    ->get();
            });

            $query = News::on($database)
                ->with(['category', 'author:id,name'])
                ->select(['id', 'title', 'description', 'image', 'category_id', 'author_id', 'created_at', 'meta_description']);

            // فلترة حسب التصنيف
            if ($request->has('category') && !empty($request->input('category'))) {
                $categorySlug = $request->input('category');
                $category = Category::on($database)
                    ->where('slug', $categorySlug)
                    ->first();

                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }

            // ترتيب الأخبار من الأحدث إلى الأقدم
            $query->orderBy('created_at', 'desc');

            $perPage = min($request->input('per_page', 10), 50); // تحديد عدد العناصر في الصفحة
            $news = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'news' => $news,
                'categories' => $categories,
                'database' => $database
            ]);

        } catch (\Exception $e) {
            Log::error('Error in FrontendNewsController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأخبار',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $database, string $id)
    {
        try {
            $database = $this->getConnection($request, $database);

            // استخدام Cache للخبر الواحد
            $cacheKey = "news_{$database}_{$id}";
            $news = Cache::remember($cacheKey, 1800, function () use ($database, $id) {
                return News::on($database)
                    ->with(['category:id,name,slug', 'author:id,name'])
                    ->findOrFail($id);
            });

            // معالجة الكلمات الدلالية
            if ($news->keywords) {
                $news->description = $this->replaceKeywordsWithLinks($news->description, $news->keywords);
            }

            return response()->json([
                'success' => true,
                'news' => $news,
                'database' => $database
            ]);

        } catch (\Exception $e) {
            Log::error('Error in FrontendNewsController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الخبر',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function category(Request $request, $database, $categorySlug)
    {
        try {
            $database = $this->getConnection($request, $database);

            $cacheKey = "category_news_{$database}_{$categorySlug}";
            
            return Cache::remember($cacheKey, 1800, function () use ($database, $categorySlug, $request) {
                $category = Category::on($database)
                    ->where('slug', $categorySlug)
                    ->firstOrFail();

                $perPage = min($request->input('per_page', 10), 50);
                
                $news = News::on($database)
                    ->where('category_id', $category->id)
                    ->with(['category:id,name,slug', 'author:id,name'])
                    ->select(['id', 'title', 'description', 'image', 'category_id', 'author_id', 'created_at', 'meta_description'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'news' => $news,
                    'category' => $category
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error in FrontendNewsController@category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأخبار حسب التصنيف',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function replaceKeywordsWithLinks($description, $keywords)
    {
        if (empty($description) || empty($keywords)) {
            return $description;
        }

        if (is_string($keywords)) {
            $keywords = array_map('trim', explode(',', $keywords));
        }

        foreach ($keywords as $keyword) {
            $keywordText = $keyword->keyword ?? $keyword;
            if (empty($keywordText)) continue;

            $database = session('database', 'jo');
            $keywordLink = route('keywords.indexByKeyword', [
                'database' => $database,
                'keywords' => $keywordText
            ]);
            
            $description = preg_replace(
                '/\b' . preg_quote($keywordText, '/') . '\b/u',
                '<a href="' . $keywordLink . '">' . $keywordText . '</a>',
                $description
            );
        }

        return $description;
    }
}
