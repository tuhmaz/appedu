<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
  public function index(Request $request)
  {
    $country = $request->input('country', 'jordan');
    $connection = null;
    switch ($country) {
      case 'jordan':
        $connection = 'jo';
        break;
      case 'saudi':
        $connection = 'sa';
        break;
      case 'egypt':
        $connection = 'eg';
        break;
      case 'palestine':
        $connection = 'ps';
        break;
      default:
        return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
    }
        $categories = DB::connection($connection)->table('categories')->get();

        return view('dashboard.categories.index', compact('categories', 'country'));
    }

    public function create()
    {
        return view('dashboard.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'country' => 'required'
        ]);

        $connection = null;
        switch ($request->country) {
            case 'jordan':
                $connection = 'jo';
                break;
            case 'saudi':
                $connection = 'sa';
                break;
            case 'egypt':
                $connection = 'eg';
                break;
            case 'palestine':
                $connection = 'ps';
                break;
            default:
                return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
        }

        Category::on($connection)->create($request->all());

        return redirect()->route('categories.index', ['country' => $request->country])->with('success', 'Category created successfully.');
    }

    public function edit($id, Request $request)
    {
        $country = $request->input('country', 'jordan');
        $connection = null;
        switch ($country) {
            case 'jordan':
                $connection = 'jo';
                break;
            case 'saudi':
                $connection = 'sa';
                break;
            case 'egypt':
                $connection = 'eg';
                break;
            case 'palestine':
                $connection = 'ps';
                break;
            default:
                return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
        }

        $category = Category::on($connection)->findOrFail($id);
        return view('dashboard.categories.edit', compact('category', 'country'));
    }

    public function update(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = null;
        switch ($country) {
            case 'jordan':
                $connection = 'jo';
                break;
            case 'saudi':
                $connection = 'sa';
                break;
            case 'egypt':
                $connection = 'eg';
                break;
            case 'palestine':
                $connection = 'ps';
                break;
            default:
                return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
        }

        $category = Category::on($connection)->findOrFail($id);
        $category->update($request->all());

        return redirect()->route('categories.index', ['country' => $country])->with('success', 'Category updated successfully.');
    }

    public function destroy($id, Request $request)
    {
        $country = $request->input('country', 'jordan');
        $connection = null;
        switch ($country) {
            case 'jordan':
                $connection = 'jo';
                break;
            case 'saudi':
                $connection = 'sa';
                break;
            case 'egypt':
                $connection = 'eg';
                break;
            case 'palestine':
                $connection = 'ps';
                break;
            default:
                return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
        }

        $category = Category::on($connection)->findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index', ['country' => $country])->with('success', 'Category deleted successfully.');
    }

    public function show($database, $category)
{
    // جلب الفئة وعرضها
    $category = Category::where('slug', $category)->firstOrFail();

    return view('categories.show', compact('category'));
}
}
