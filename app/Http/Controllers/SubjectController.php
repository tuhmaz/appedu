<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
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

        $subjects = Subject::on($connection)->with('schoolClass')->get();
        $groupedSubjects = $subjects->groupBy(function ($subject) {
            return $subject->schoolClass->grade_name;
        });

        return view('dashboard.subjects.index', compact('groupedSubjects', 'country'));
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

        return view('dashboard.subjects.create', compact('classes', 'country'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'grade_level' => 'required|exists:school_classes,grade_level'
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

        Subject::on($connection)->create([
            'subject_name' => $request->subject_name,
            'grade_level' => $request->grade_level
        ]);

        return redirect()->route('subjects.index', ['country' => $country])->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject)
    {
        return view('dashboard.subjects.show', compact('subject'));
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

        $subject = Subject::on($connection)->findOrFail($id);
        $classes = SchoolClass::on($connection)->get();

        return view('dashboard.subjects.edit', compact('subject', 'classes', 'country'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'grade_level' => 'required|exists:school_classes,grade_level'
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

        $subject->setConnection($connection)->update([
            'subject_name' => $request->subject_name,
            'grade_level' => $request->grade_level
        ]);

        return redirect()->route('subjects.index', ['country' => $country])->with('success', 'Subject updated successfully.');
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

        $subject = Subject::on($connection)->findOrFail($id);
        $subject->delete();

        return redirect()->route('subjects.index', ['country' => $country])->with('success', 'Subject deleted successfully.');
    }
}
