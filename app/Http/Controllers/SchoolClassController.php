<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;



class SchoolClassController extends Controller
{
  use HasFactory, Notifiable;

  public function index(Request $request)
  {
    $country = $request->input('country', 'jordan');
    $connection = null;
    switch ($country) {
      case 'jordan':
        $connection = 'mysql';
        break;
      case 'saudi':
        $connection = 'subdomain1';
        break;
      case 'egypt':
        $connection = 'subdomain2';
        break;
      case 'palestine':
        $connection = 'subdomain3';
        break;
      default:
        return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
    }
    $schoolClasses = DB::connection($connection)->table('school_classes')->get();
    return view('dashboard.classes.index', compact('schoolClasses', 'country'));
  }
  public function create()
  {
    return view('dashboard.classes.create');
  }

  public function store(Request $request)
  {

    $request->validate([
      'grade_name' => 'required|string|max:255',
      'grade_level' => 'required|integer',
      'country' => 'required|string',
    ]);


    $connection = null;
    switch ($request->input('country')) {

      case 'jordan':
        $connection = 'mysql';
        break;
      case 'saudi':
        $connection = 'subdomain1';
        break;
      case 'egypt':
        $connection = 'subdomain2';
        break;
      case 'palestine':
        $connection = 'subdomain3';
        break;
      default:
        return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
    }
    if ($connection) {
      DB::connection($connection)->table('school_classes')->insert([
        'grade_name' => $request->input('grade_name'),
        'grade_level' => $request->input('grade_level'),
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      return redirect()->route('classes.index')->with('success', 'Class added successfully in ' . $request->input('country') . ' database.');
    }
    return redirect()->back()->withErrors(['country' => 'No valid database selected']);
  }



  private function updateSidebar()
  {

    View::share('classes', SchoolClass::all());
  }


  public function show(SchoolClass $class, Request $request)
  {

    if ($request->is('dashboard/*')) {

      return view('dashboard.classes.show', compact('class'));
    } else {

      $subjects = Subject::where('grade_level', $class->grade_level)->get();
      return view('frontend.class.show', compact('class', 'subjects'));
    }
  }


  public function edit(Request $request, $id)
  {
    $country = $request->input('country');
    switch ($country) {
      case 'jordan':
        $connection = 'mysql';
        break;
      case 'saudi':
        $connection = 'subdomain1';
        break;
      case 'egypt':
        $connection = 'subdomain2';
        break;
      case 'palestine':
        $connection = 'subdomain3';
        break;
      default:
        return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
    }
    $class = DB::connection($connection)->table('school_classes')->where('id', $id)->first();
    if (!$class) {
      return redirect()->back()->withErrors(['class' => 'Class not found']);
    }
    return view('dashboard.classes.edit', compact('class', 'country'));
  }



  public function update(Request $request, $id)
  {
    $request->validate([
      'grade_name' => 'required|string|max:255',
      'grade_level' => 'required|integer',
      'country' => 'required|string',
    ]);

    switch ($request->input('country')) {
      case 'jordan':
        $connection = 'mysql';
        break;
      case 'saudi':
        $connection = 'subdomain1';
        break;
      case 'egypt':
        $connection = 'subdomain2';
        break;
      case 'palestine':
        $connection = 'subdomain3';
        break;
      default:
        return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
    }
    DB::connection($connection)->table('school_classes')->where('id', $id)->update([
      'grade_name' => $request->input('grade_name'),
      'grade_level' => $request->input('grade_level'),
      'updated_at' => now(),
    ]);

    return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
  }

  public function destroy(Request $request, $id)
  {
    $country = $request->input('country');
    switch ($country) {
      case 'jordan':
        $connection = 'mysql';
        break;
      case 'saudi':
        $connection = 'subdomain1';
        break;
      case 'egypt':
        $connection = 'subdomain2';
        break;
      case 'palestine':
        $connection = 'subdomain3';
        break;
      default:
        return redirect()->back()->withErrors(['country' => 'Invalid country selected']);
    }
    DB::connection($connection)->table('school_classes')->where('id', $id)->delete();

    return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
  }
}
