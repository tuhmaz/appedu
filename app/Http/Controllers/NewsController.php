<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{


  private function getConnection(string $country): string
  {
      switch ($country) {
          case 'saudi':
              return 'subdomain1';
          case 'egypt':
              return 'subdomain2';
          case 'palestine':
              return 'subdomain3';
          default:
              return 'mysql';
      }
  }


    public function index(Request $request)
    {
         $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

         $query = News::on($connection);

         if ($request->has('category') && !empty($request->input('category'))) {
            $query->where('category', $request->input('category'));
        }

         $news = $query->paginate(10);

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

        return view('dashboard.news.index', compact('news', 'categories', 'country'));
    }


    public function show($id, Request $request)
    {


      $database = $request->get('database', session('database', 'mysql'));


      config()->set('database.default', $database);



        $news = News::findOrFail($id);


        if ($request->is('dashboard/*')) {
            return view('dashboard.news.show', compact('news', 'country'));
        } else {
            return view('frontend.news.show', compact('news' ));
        }
    }






    public function create(Request $request)
    {
        $country = $request->input('country', 'jordan');
        return view('dashboard.news.create', compact('country'));
    }

    public function store(Request $request)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $request->validate([
            'title' => 'required|max:60',
            'description' => 'required',
            'category' => 'required',
            'meta_description' => 'required|max:160',
            'keywords' => 'required',
            'image' => 'image|nullable|max:6999'
        ]);

        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('image')->storeAs('public/images', $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.jpg';
        }

        News::on($connection)->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'meta_description' => $request->input('meta_description'),
            'keywords' => $request->input('keywords'),
            'image' => $fileNameToStore,
            'alt' => $request->file('image') ? $filename : 'no image',
        ]);

        return redirect()->route('news.index', ['country' => $country])->with('success', 'News Created');
    }

    public function edit(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $news = News::on($connection)->findOrFail($id);
        return view('dashboard.news.edit', compact('news', 'country'));
    }

    public function update(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $request->validate([
            'title' => 'required|max:60',
            'description' => 'required',
            'category' => 'required',
            'meta_description' => 'required|max:160',
            'keywords' => 'required',
            'image' => 'nullable|image|max:6999'
        ]);

        $news = News::on($connection)->findOrFail($id);
        $news->update($request->only('title', 'description', 'category', 'meta_description', 'keywords'));

        if ($request->hasFile('image')) {
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('image')->storeAs('public/images', $fileNameToStore);
            $news->update([
                'image' => $fileNameToStore,
                'alt' => $filename,
            ]);
        }

        return redirect()->route('news.index', ['country' => $country])->with('success', 'News Updated');
    }

    public function destroy(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $news = News::on($connection)->findOrFail($id);
        if ($news->image != 'noimage.jpg') {
            Storage::delete('public/images/' . $news->image);
        }
        $news->delete();

        return redirect()->route('news.index', ['country' => $country])->with('success', 'News Deleted');
    }
}
