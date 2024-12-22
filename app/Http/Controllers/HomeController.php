<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\File;
use App\Models\Category;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\User;

class HomeController extends Controller
{
  public function setDatabase(Request $request)
  {
    $request->validate([
      'database' => 'required|string|in:jo,sa,eg,ps'
    ]);


    $request->session()->put('database', $request->input('database'));


    return redirect()->route('home');
  }

  /**
   *
   */
  private function getDatabaseConnection(): string
  {
    return session('database', 'jo');
  }

  /**
   *
   */
  public function index(Request $request)
  {
      $database = $this->getDatabaseConnection();
      
      // Increase cache duration and eager load relationships
      $news = Cache::remember("news_{$database}", 3600, function () use ($database) {
          $newsQuery = News::on($database)
              ->with('category')
              ->latest()
              ->take(10)
              ->get();

          // تحميل علاقة المؤلف من قاعدة البيانات الرئيسية
          foreach ($newsQuery as $newsItem) {
              $newsItem->setRelation('author', User::find($newsItem->author_id));
          }

          return $newsQuery;
      });

      // Cache classes with longer duration
      $classes = Cache::remember("classes_{$database}", 3600, function () use ($database) {
          return SchoolClass::on($database)->get();
      });

      // Cache categories
      $categories = Cache::remember("categories_{$database}", 3600, function () use ($database) {
          return Category::on($database)->get();
      });

      // Optimize file query with eager loading and pagination
      $query = File::on($database)->with(['article.semester.subject.schoolClass']);

      if ($request->filled('class_id')) {
          $query->whereHas('article.semester.subject.schoolClass', function ($q) use ($request) {
              $q->where('id', $request->class_id);
          });
      }

      if ($request->filled('subject_id')) {
          $query->whereHas('article.semester.subject', function ($q) use ($request) {
              $q->where('id', $request->subject_id);
          });
      }

      if ($request->filled('semester_id')) {
          $query->whereHas('article.semester', function ($q) use ($request) {
              $q->where('id', $request->semester_id);
          });
      }

      if ($request->filled('file_category')) {
          $query->where('file_category', $request->file_category);
      }

      // Paginate files instead of getting all
      $files = $query->latest()->paginate(20);

      // Cache calendar data
      $calendarKey = "calendar_{$database}_{$request->input('month', date('m'))}_{$request->input('year', date('Y'))}";
      $calendarData = Cache::remember($calendarKey, 3600, function () use ($request) {
          $month = $request->input('month', date('m'));
          $year = $request->input('year', date('Y'));
          $date = Carbon::createFromDate($year, $month, 1);
          
          return [
              'date' => $date,
              'days' => $this->generateCalendarDays($date),
          ];
      });

      // جلب الأحداث مع الكاش
      $events = Cache::remember("events", 3600, function () {
          return Event::on('jo')  // استخدام قاعدة البيانات الرئيسية فقط
              ->whereDate('event_date', '>=', now()->startOfMonth())
              ->whereDate('event_date', '<=', now()->endOfMonth())
              ->orderBy('event_date')
              ->get();
      });

      return view('content.pages.home', compact(
          'news',
          'classes',
          'categories',
          'files',
          'calendarData',
          'events'
      ));
  }

  private function generateCalendarDays($date)
  {
      $startOfCalendar = $date->copy()->startOfMonth()->startOfWeek(Carbon::FRIDAY);
      $endOfCalendar = $date->copy()->endOfMonth()->endOfWeek(Carbon::FRIDAY);
      
      $days = collect();
      $currentDate = $startOfCalendar->copy();
      
      while ($currentDate <= $endOfCalendar) {
          $days->push($currentDate->copy());
          $currentDate->addDay();
      }
      
      return $days;
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
