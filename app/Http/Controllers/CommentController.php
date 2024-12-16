<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\News;
use App\Models\Article;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required',
            'commentable_id' => 'required',
            'commentable_type' => 'required',
        ]);

        // الحصول على اسم الاتصال الحالي من الجلسة
        $connection = session('database', 'jo');

        // إنشاء التعليق في قاعدة البيانات الرئيسية مع تحديد connection_name
        Comment::create([
            'body' => $request->body,
            'user_id' => auth()->id(),
            'commentable_id' => $request->commentable_id,
            'commentable_type' => $request->commentable_type,
            'connection_name' => $connection,
        ]);

        return redirect()->back()->with('success', 'تم إضافة التعليق بنجاح!');
    }

    public function index($commentable_type, $commentable_id)
    {
        // الحصول على اسم الاتصال الحالي
        $connection = session('database', 'jo');

        // جلب التعليقات من قاعدة البيانات الرئيسية مع تصفية حسب connection_name
        $comments = Comment::with('user')
            ->where('commentable_type', $commentable_type)
            ->where('commentable_id', $commentable_id)
            ->where('connection_name', $connection)
            ->latest()
            ->get();

        return response()->json($comments);
    }
}
