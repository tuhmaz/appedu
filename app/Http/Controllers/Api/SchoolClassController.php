<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Traits\Cacheable;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
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
     * عرض قائمة الصفوف الدراسية
     * كاش لمدة 24 ساعة لأنها بيانات ثابتة نسبياً
     */
    public function index(Request $request)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);
        
        $key = $this->createCacheKey('classes.all', $country);
        
        return $this->cache()->remember($key, function () use ($connection) {
            return SchoolClass::on($connection)
                ->orderBy('grade_level')
                ->get()
                ->groupBy('grade_name');
        }, $this->getCacheDuration('static'));
    }

    /**
     * عرض صف دراسي محدد
     * كاش لمدة 12 ساعة
     */
    public function show(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);
        
        $key = $this->createCacheKey('class', $id, $country);
        
        return $this->cache()->remember($key, function () use ($connection, $id) {
            return SchoolClass::on($connection)
                ->with(['subjects'])
                ->findOrFail($id);
        }, $this->getCacheDuration('static'));
    }

    /**
     * إنشاء صف دراسي جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'grade_name' => 'required|string',
            'grade_level' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $class = SchoolClass::on($connection)->create([
            'grade_name' => $request->grade_name,
            'grade_level' => $request->grade_level,
            'description' => $request->description
        ]);

        // حذف الكاش ذو الصلة
        $this->clearRelatedCache([
            $this->createCacheKey('classes.all', $country)
        ]);

        return response()->json([
            'message' => 'School class created successfully',
            'class' => $class
        ], 201);
    }

    /**
     * تحديث صف دراسي
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'grade_name' => 'required|string',
            'grade_level' => 'required|integer',
            'description' => 'nullable|string'
        ]);

        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $class = SchoolClass::on($connection)->findOrFail($id);
        $class->update([
            'grade_name' => $request->grade_name,
            'grade_level' => $request->grade_level,
            'description' => $request->description
        ]);

        // حذف الكاش ذو الصلة
        $this->clearRelatedCache([
            $this->createCacheKey('classes.all', $country),
            $this->createCacheKey('class', $id, $country)
        ]);

        return response()->json([
            'message' => 'School class updated successfully',
            'class' => $class
        ]);
    }

    /**
     * حذف صف دراسي
     */
    public function destroy(Request $request, $id)
    {
        $country = $request->input('country', 'jordan');
        $connection = $this->getConnection($country);

        $class = SchoolClass::on($connection)->findOrFail($id);
        $class->delete();

        // حذف الكاش ذو الصلة
        $this->clearRelatedCache([
            $this->createCacheKey('classes.all', $country),
            $this->createCacheKey('class', $id, $country)
        ]);

        return response()->json([
            'message' => 'School class deleted successfully'
        ]);
    }
}
