<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Traits\Cacheable;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    use Cacheable;

    private function getConnection(string $country): string
    {
        return match ($country) {
            'jordan' => 'jordan',
            'palestine' => 'palestine',
            default => 'jordan'
        };
    }

    /**
     * عرض قائمة الفصول الدراسية
     * كاش لمدة 24 ساعة لأنها بيانات ثابتة نسبياً
     */
    public function index(Request $request)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);
        
        $key = $this->createCacheKey('semesters.all', $country);
        
        return $this->cache()->remember($key, function () use ($connection) {
            return Semester::on($connection)
                ->with(['subjects:id,name'])
                ->orderBy('name')
                ->get();
        }, $this->getCacheDuration('static'));
    }

    /**
     * عرض فصل دراسي محدد
     * كاش لمدة 12 ساعة
     */
    public function show(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);
        
        $key = $this->createCacheKey('semester', $id, $country);
        
        return $this->cache()->remember($key, function () use ($connection, $id) {
            return Semester::on($connection)
                ->with(['subjects', 'articles'])
                ->findOrFail($id);
        }, $this->getCacheDuration('static'));
    }

    /**
     * إنشاء فصل دراسي جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $semester = Semester::on($connection)->create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        // حذف الكاش ذو الصلة
        $this->clearRelatedCache([
            $this->createCacheKey('semesters.all', $country)
        ]);

        return response()->json([
            'message' => 'Semester created successfully',
            'semester' => $semester
        ], 201);
    }

    /**
     * تحديث فصل دراسي
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $semester = Semester::on($connection)->findOrFail($id);
        $semester->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        // حذف الكاش ذو الصلة
        $this->clearRelatedCache([
            $this->createCacheKey('semesters.all', $country),
            $this->createCacheKey('semester', $id, $country)
        ]);

        return response()->json([
            'message' => 'Semester updated successfully',
            'semester' => $semester
        ]);
    }

    /**
     * حذف فصل دراسي
     */
    public function destroy(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $semester = Semester::on($connection)->findOrFail($id);
        $semester->delete();

        // حذف الكاش ذو الصلة
        $this->clearRelatedCache([
            $this->createCacheKey('semesters.all', $country),
            $this->createCacheKey('semester', $id, $country)
        ]);

        return response()->json([
            'message' => 'Semester deleted successfully'
        ]);
    }

    /**
     * الحصول على الفصول الدراسية لمادة معينة
     * كاش لمدة 6 ساعات
     */
    public function getSemestersBySubject(Request $request, $subjectId)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);
        
        $key = $this->createCacheKey('semesters.subject', $subjectId, $country);
        
        return $this->cache()->remember($key, function () use ($connection, $subjectId) {
            return Semester::on($connection)
                ->whereHas('subjects', function ($query) use ($subjectId) {
                    $query->where('subjects.id', $subjectId);
                })
                ->with(['subjects' => function ($query) use ($subjectId) {
                    $query->where('subjects.id', $subjectId);
                }])
                ->get();
        }, $this->getCacheDuration('dynamic'));
    }
}
