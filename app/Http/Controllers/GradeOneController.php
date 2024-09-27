<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Article;
use App\Models\File;

class GradeOneController extends Controller
{
    public function setDatabase(Request $request)
    {
        $request->validate([
            'database' => 'required|string|in:mysql,sa,subdomain2,subdomain3'
        ]);


        $request->session()->put('database', $request->input('database'));

        return redirect()->back();
    }

    private function getConnection(Request $request)
    {

        return $request->session()->get('database', 'mysql');
    }

    public function index(Request $request)
    {
        $database = $this->getConnection($request);


        $lesson = SchoolClass::on($database)->get();
        $classes = SchoolClass::on($database)->get();

        return view('frontend.class.index', compact('lesson', 'classes'));
    }

    public function show(Request $request, $id)
    {
        $database = $this->getConnection($request);


        $class = SchoolClass::on($database)->findOrFail($id);

        return view('frontend.class.show', compact('class', 'database'));
    }



    public function showSubject(Request $request, $id)
    {

        $database = $request->session()->get('database', 'mysql');


        $subject = Subject::on($database)->findOrFail($id);


        $gradeLevel = $subject->grade_level;
        $semesters = Semester::on($database)->where('grade_level', $gradeLevel)->get();


        return view('frontend.subject.show', compact('subject', 'semesters', 'database'));
    }

    public function subjectArticles(Request $request, Subject $subject, Semester $semester, $category)
{

    $database = $request->input('database', session('database', 'mysql'));


    $articles = Article::on($database)
        ->where('subject_id', $subject->id)
        ->where('semester_id', $semester->id)
        ->whereHas('files', function ($query) use ($category) {
            $query->where('file_category', $category);
        })
        ->with(['files' => function ($query) use ($category) {
            $query->where('file_category', $category);
        }])
        ->get();


    $grade_level = $subject->schoolClass->grade_name;

    return view('frontend.articles.index', compact('articles', 'subject', 'semester', 'category', 'grade_level', 'database'));
}


public function showArticle(Request $request, $id)
{
    // استعادة قاعدة البيانات الافتراضية من الجلسة أو الاتصال الافتراضي
    $database = $request->input('database', session('database', 'mysql'));

    // جلب المقالة من قاعدة البيانات الفرعية المحددة
    $article = Article::on($database)->findOrFail($id);

    // جلب الملفات والتصنيفات الأخرى
    $file = $article->files()->first();
    $category = $file ? $file->file_category : 'articles';

    $subject = $article->subject;
    $semester = $article->semester;
    $grade_level = $subject->schoolClass->grade_name;

    // تحديث عدد الزيارات
    $article->increment('visit_count');

    $contentWithKeywords = $this->replaceKeywordsWithLinks($article->content, $article->keywords);
    $article->content = $this->createInternalLinks($article->content, $article->keywords);

    return view('frontend.articles.show', compact('article', 'subject', 'semester', 'grade_level', 'category', 'database' ,'contentWithKeywords'));
}

private function createInternalLinks($content, $keywords)
{
    $keywordsArray = $keywords->pluck('keyword')->toArray();

    foreach ($keywordsArray as $keyword) {
        $keyword = trim($keyword);
        $url = route('articles.indexByKeyword', ['keywords' => $keyword]); // تأكد من تمرير keywords بشكل صحيح
        $content = str_replace($keyword, '<a href="' . $url . '">' . $keyword . '</a>', $content);
    }

    return $content;
}

private function replaceKeywordsWithLinks($content, $keywords)
{
    foreach ($keywords as $keyword) {
        $keywordText = $keyword->keyword;
        $keywordLink = route('articles.indexByKeyword', ['keywords' => $keywordText]); // تأكد من تمرير keywords بشكل صحيح

        // استبدال الكلمة الدلالية بالرابط الخاص بها
        $content = preg_replace('/\b' . preg_quote($keywordText, '/') . '\b/', '<a href="' . $keywordLink . '">' . $keywordText . '</a>', $content);
    }

    return $content;
}



    public function downloadFile(Request $request, $id)
    {
        $database = $this->getConnection($request);


        $file = File::on($database)->findOrFail($id);


        $file->increment('download_count');


        $filePath = storage_path('app/public/' . $file->file_path);
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'File not found.');
    }


}
