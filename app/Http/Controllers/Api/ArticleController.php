<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Get(
 *     path="/api/articles",
 *     tags={"Articles"},
 *     summary="Get paginated list of articles",
 *     @OA\Parameter(
 *         name="country",
 *         in="query",
 *         description="Country code (e.g., jordan, saudi, egypt, palestine)",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="success"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Article")
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(
 *                     property="current_page",
 *                     type="integer",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="last_page",
 *                     type="integer",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="per_page",
 *                     type="integer",
 *                     example=25
 *                 ),
 *                 @OA\Property(
 *                     property="total",
 *                     type="integer",
 *                     example=100
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query()->with(['keywords', 'author']);

        // Filter by country
        $country = $request->input('country', 'jordan');
        $query->where('country', $country);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest()->paginate(25);

        return response()->json([
            'status' => 'success',
            'data' => $articles->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total()
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Create a new article",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "country"},
     *             @OA\Property(property="title", type="string", example="Introduction to Mathematics"),
     *             @OA\Property(property="content", type="string", example="This article explains basic mathematics concepts."),
     *             @OA\Property(
     *                 property="country",
     *                 type="string",
     *                 example="jordan",
     *                 enum={"jordan", "saudi", "egypt", "palestine"}
     *             ),
     *             @OA\Property(
     *                 property="keywords",
     *                 type="array",
     *                 @OA\Items(type="string", example="math")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Article created successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Article"
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'country' => 'required|string|in:jordan,saudi,egypt,palestine',
            'keywords' => 'nullable|string',
        ]);

        $article = Article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'country' => $validated['country'],
            'author_id' => Auth::id(),
        ]);

        if (!empty($validated['keywords'])) {
            $keywords = explode(',', $validated['keywords']);
            foreach ($keywords as $keyword) {
                $keywordModel = Keyword::firstOrCreate(['keyword' => trim($keyword)]);
                $article->keywords()->attach($keywordModel->id);
            }
        }

        $article->load(['keywords', 'author']);

        return response()->json([
            'status' => 'success',
            'message' => 'Article created successfully',
            'data' => $article
        ], 201);
    }
}
