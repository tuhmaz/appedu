<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $country = $request->input('country', 'jordan');

        switch ($country) {
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
                $connection = 'mysql';
        }

        $semesters = Semester::on($connection)->with('schoolClass')->get();

        $groupedSemesters = $semesters->groupBy(function ($semester) {
            return $semester->schoolClass->grade_name;
        });

        return view('dashboard.semesters.index', compact('groupedSemesters', 'country'));
    }

    public function create(Request $request)
    {
        $country = $request->input('country', 'jordan');
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
                $connection = 'mysql';
        }
        $classes = SchoolClass::on($connection)->get();
        return view('dashboard.semesters.create', compact('classes', 'country'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester_name' => 'required|string|max:255',
            'grade_level' => 'required|exists:school_classes,id',
        ]);

        $country = $request->input('country', 'jordan');
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
                $connection = 'mysql';
        }
        Semester::on($connection)->create([
            'semester_name' => $request->semester_name,
            'grade_level' => $request->grade_level,
        ]);

        return redirect()->route('semesters.index', ['country' => $country])->with('success', 'Semester created successfully.');
    }

    public function show(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');

        switch ($country) {
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
                $connection = 'mysql';
        }

        $semester = Semester::on($connection)->findOrFail($id);

        return view('dashboard.semesters.show', compact('semester', 'country'));
    }

    public function edit(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
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
                $connection = 'mysql';
        }

        $semester = Semester::on($connection)->findOrFail($id);
        $classes = SchoolClass::on($connection)->get();

        return view('dashboard.semesters.edit', compact('semester', 'classes', 'country'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'semester_name' => 'required|string|max:255',
            'grade_level' => 'required|exists:school_classes,id',
        ]);

        $country = $request->input('country', 'jordan');
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
                $connection = 'mysql';
        }

        $semester = Semester::on($connection)->findOrFail($id);
        $semester->update([
            'semester_name' => $request->semester_name,
            'grade_level' => $request->grade_level,
        ]);

        return redirect()->route('semesters.index', ['country' => $country])->with('success', 'Semester updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
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
                $connection = 'mysql';
        }

        $semester = Semester::on($connection)->findOrFail($id);
        $semester->delete();

        return redirect()->route('semesters.index', ['country' => $country])->with('success', 'Semester deleted successfully.');
    }
}

