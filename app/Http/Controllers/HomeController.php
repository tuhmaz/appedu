<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\File;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
  public function setDatabase(Request $request)
  {
      $request->validate([
          'database' => 'required|string|in:mysql,subdomain1,subdomain2,subdomain3'
      ]);


      $request->session()->put('database', $request->input('database'));


        return redirect()->route('home');
    }

    /**
     *
     */
    private function getDatabaseConnection(): string
    {
        return session('database', 'mysql');
    }

    /**
     *
     */
    public function index(Request $request)
    {

        $database = $this->getDatabaseConnection();


        $news = Cache::remember("news_{$database}", 60, function () use ($database) {
            return News::on($database)->get();
        });

        $classes = Cache::remember("classes_{$database}", 60, function () use ($database) {
            return SchoolClass::on($database)->get();
        });


        $query = File::on($database);


        if ($request->class_id) {
            $query->whereHas('article.semester.subject.schoolClass', function ($q) use ($request) {
                $q->where('id', $request->class_id);
            });
        }

        if ($request->subject_id) {
            $query->whereHas('article.semester.subject', function ($q) use ($request) {
                $q->where('id', $request->subject_id);
            });
        }

        if ($request->semester_id) {
            $query->whereHas('article.semester', function ($q) use ($request) {
                $q->where('id', $request->semester_id);
            });
        }

        if ($request->file_category) {
            $query->where('file_category', $request->file_category);
        }

        $files = $query->get();


        $categories = [
            'extra_teachers' => __('Extra Teachers'),
            'ministry_news' => __('Ministry News'),
            'school_management' => __('School Management'),
            'school_broadcast' => __('School Broadcast'),
            'evaluation_tools' => __('Evaluation Tools'),
            'general_information' => __('General Information'),
            'administrative_plans' => __('Administrative Plans'),
            'miscellaneous' => __('Miscellaneous'),
            'teacher_promotions' => __('Teacher Promotions'),
            'achievement_files' => __('Achievement Files'),
        ];


        if (Auth::check()) {
            $user = Auth::user();
            return view('content.pages.home', compact('user', 'news', 'classes', 'categories', 'files'));
        } else {
            return view('content.pages.home', compact('news', 'classes', 'categories', 'files'));
        }
    }

    public function about()
    {
        return view('about');
    }

    public function contact()
    {
        return view('contact');
    }
}
