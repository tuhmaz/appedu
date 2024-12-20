<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            
            $categories = Category::on($database)
                ->select('id', 'name', 'slug')
                ->get();

            $query = News::on($database)->with('category');

            // فلترة حسب التصنيف
            if ($request->has('category') && !empty($request->input('category'))) {
                $categorySlug = $request->input('category');
                $category = Category::on($database)
                    ->where('slug', $categorySlug)
                    ->first();

                if ($category) {
                    $query->where('category_id', $category->id);
                } else {
                    $query->whereNull('category_id');
                }
            }

            // ترتيب الأخبار من الأحدث إلى الأقدم
            $query->orderBy('created_at', 'desc');

            $news = $query->paginate(10);

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

            $news = News::on($database)
                ->with('category')
                ->findOrFail($id);

            // جلب المؤلف من قاعدة البيانات الرئيسية
            $news->author = User::find($news->author_id);

            // معالجة الكلمات الدلالية
            if ($news->keywords) {
                $news->description = $this->replaceKeywordsWithLinks($news->description, $news->keywords);
                $news->description = $this->createInternalLinks($news->description, $news->keywords);
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

    private function createInternalLinks($description, $keywords)
    {
        if (empty($description) || empty($keywords)) {
            return $description;
        }

        if (is_string($keywords)) {
            $keywordsArray = array_map('trim', explode(',', $keywords));
        } else {
            $keywordsArray = $keywords->pluck('keyword')->toArray();
        }

        foreach ($keywordsArray as $keyword) {
            if (empty($keyword)) continue;

            $database = session('database', 'jo');
            $url = route('keywords.indexByKeyword', [
                'database' => $database,
                'keywords' => $keyword
            ]);
            
            $description = preg_replace(
                '/\b' . preg_quote($keyword, '/') . '\b/u',
                '<a href="' . $url . '">' . $keyword . '</a>',
                $description
            );
        }

        return $description;
    }

    public function category(Request $request, $translatedCategory)
    {
        try {
            $database = $this->getConnection($request);

            $category = Category::on($database)
                ->where('name', $translatedCategory)
                ->firstOrFail();

            $categories = Category::on($database)
                ->select('id', 'name', 'slug')
                ->get();

            $news = News::on($database)
                ->where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'news' => $news,
                'categories' => $categories,
                'category' => $category
            ]);

        } catch (\Exception $e) {
            Log::error('Error in FrontendNewsController@category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأخبار حسب التصنيف',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filterNewsByCategory(Request $request)
    {
        try {
            $database = $this->getConnection($request);
            $categorySlug = $request->input('category');

            if (empty($categorySlug)) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تحديد التصنيف'
                ], 400);
            }

            $category = Category::on($database)
                ->where('slug', $categorySlug)
                ->firstOrFail();

            $news = News::on($database)
                ->where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'news' => $news,
                'category' => $category
            ]);

        } catch (\Exception $e) {
            Log::error('Error in FrontendNewsController@filterNewsByCategory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء فلترة الأخبار',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
