<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Traits\Cacheable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    use Cacheable;

    private function getConnection(string $country): string
    {
        return match ($country) {
            'saudi' => 'sa',
            'egypt' => 'eg',
            'palestine' => 'ps',
            default => 'jo',
        };
    }

    /**
     * عرض قائمة المواد
     * كاش لمدة 24 ساعة لأنها بيانات شبه ثابتة
     */
    public function index(Request $request)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $key = $this->createCacheKey('subjects.all', $country);
        
        return $this->cache()->remember($key, function () use ($request, $connection) {
            $subjects = Subject::on($connection)->with('schoolClass')->get();
            $groupedSubjects = $subjects->groupBy(function ($subject) {
                return $subject->schoolClass->grade_name;
            });

            return $groupedSubjects;
        }, $this->getCacheDuration('static'));
    }

    /**
     * عرض مادة محددة
     * كاش لمدة 6 ساعات
     */
    public function show(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $key = $this->createCacheKey('subject', $id, $country);
        
        return $this->cache()->remember($key, function () use ($request, $id, $connection) {
            return Subject::on($connection)->findOrFail($id);
        }, $this->getCacheDuration('dynamic'));
    }

    /**
     * إنشاء مادة جديدة
     * حذف الكاش ذو الصلة
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'grade_level' => 'required|exists:school_classes,grade_level',
            'country' => 'required|string'
        ]);

        $connection = $this->getConnection($request->input('country'));

        $subject = Subject::on($connection)->create([
            'subject_name' => $request->subject_name,
            'grade_level' => $request->grade_level
        ]);

        $this->clearRelatedCache([
            $this->createCacheKey('subjects.all', $request->input('country'))
        ]);

        return response()->json(['message' => 'Subject created successfully', 'subject' => $subject], 201);
    }

    /**
     * تحديث مادة
     * تحديث الكاش ذو الصلة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'grade_level' => 'required|exists:school_classes,grade_level',
            'country' => 'required|string'
        ]);

        $connection = $this->getConnection($request->input('country'));

        $subject = Subject::on($connection)->findOrFail($id);

        $subject->update([
            'subject_name' => $request->subject_name,
            'grade_level' => $request->grade_level
        ]);

        $this->clearRelatedCache([
            $this->createCacheKey('subjects.all', $request->input('country')),
            $this->createCacheKey('subject', $id, $request->input('country'))
        ]);

        return response()->json(['message' => 'Subject updated successfully', 'subject' => $subject]);
    }

    /**
     * حذف مادة
     * حذف الكاش ذو الصلة
     */
    public function destroy(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $subject = Subject::on($connection)->findOrFail($id);
        $subject->delete();

        $this->clearRelatedCache([
            $this->createCacheKey('subjects.all', $country),
            $this->createCacheKey('subject', $id, $country)
        ]);

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
