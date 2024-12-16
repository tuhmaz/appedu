<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GradeOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Grade One",
 *     description="API Endpoints for managing first grade content"
 * )
 */
class GradeOneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/grade-one",
     *     tags={"Grade One"},
     *     summary="Get list of first grade content",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="subject",
     *         in="query",
     *         description="Filter by subject",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="unit",
     *         in="query",
     *         description="Filter by unit",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in title and description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/GradeOne")
     *             ),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = GradeOne::query();

        // Filter by subject
        if ($request->has('subject')) {
            $query->where('subject', $request->input('subject'));
        }

        // Filter by unit
        if ($request->has('unit')) {
            $query->where('unit', $request->input('unit'));
        }

        // Search in title and description
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Order by order field and then by created date
        $query->orderBy('order', 'asc')
              ->orderBy('created_at', 'desc');

        return $query->paginate(10);
    }

    /**
     * @OA\Post(
     *     path="/api/grade-one",
     *     tags={"Grade One"},
     *     summary="Create new first grade content",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GradeOneRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Content created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/GradeOne")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:50',
            'unit' => 'required|string|max:100',
            'lesson' => 'required|string|max:100',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string',
            'order' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $gradeOne = GradeOne::create($request->all());

        return response()->json($gradeOne, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/grade-one/{id}",
     *     tags={"Grade One"},
     *     summary="Get first grade content by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Content ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/GradeOne")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Content not found"
     *     )
     * )
     */
    public function show($id)
    {
        $gradeOne = GradeOne::find($id);
        
        if (!$gradeOne) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        return response()->json($gradeOne);
    }

    /**
     * @OA\Put(
     *     path="/api/grade-one/{id}",
     *     tags={"Grade One"},
     *     summary="Update first grade content",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Content ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GradeOneRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Content updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/GradeOne")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Content not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $gradeOne = GradeOne::find($id);
        
        if (!$gradeOne) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:50',
            'unit' => 'required|string|max:100',
            'lesson' => 'required|string|max:100',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string',
            'order' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $gradeOne->update($request->all());

        return response()->json($gradeOne);
    }

    /**
     * @OA\Delete(
     *     path="/api/grade-one/{id}",
     *     tags={"Grade One"},
     *     summary="Delete first grade content",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Content ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Content deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Content not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $gradeOne = GradeOne::find($id);
        
        if (!$gradeOne) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        // Delete any associated files if needed
        if (!empty($gradeOne->attachments)) {
            foreach ($gradeOne->attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment)) {
                    Storage::disk('public')->delete($attachment);
                }
            }
        }

        $gradeOne->delete();

        return response()->json(['message' => 'Content deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/grade-one/{id}/attachment",
     *     tags={"Grade One"},
     *     summary="Upload attachment for first grade content",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Content ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="attachment",
     *                     type="string",
     *                     format="binary",
     *                     description="Attachment file"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attachment uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="attachment", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function uploadAttachment(Request $request, $id)
    {
        $gradeOne = GradeOne::find($id);
        
        if (!$gradeOne) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'attachment' => 'required|file|max:10240' // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Store attachment
        $attachmentName = 'grade-one/' . Str::random(40) . '.' . $request->attachment->extension();
        $request->attachment->storeAs('public', $attachmentName);

        // Update content attachments
        $attachments = $gradeOne->attachments ?? [];
        $attachments[] = $attachmentName;
        $gradeOne->attachments = $attachments;
        $gradeOne->save();

        return response()->json([
            'attachment' => Storage::url($attachmentName),
            'message' => 'Attachment uploaded successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/grade-one/{id}/attachment/{filename}",
     *     tags={"Grade One"},
     *     summary="Delete attachment from first grade content",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Content ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filename",
     *         in="path",
     *         description="Attachment filename",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attachment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Content or attachment not found"
     *     )
     * )
     */
    public function deleteAttachment($id, $filename)
    {
        $gradeOne = GradeOne::find($id);
        
        if (!$gradeOne) {
            return response()->json(['message' => 'Content not found'], 404);
        }

        $attachments = $gradeOne->attachments ?? [];
        $key = array_search($filename, $attachments);

        if ($key === false) {
            return response()->json(['message' => 'Attachment not found'], 404);
        }

        // Delete file
        if (Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->delete($filename);
        }

        // Remove from attachments array
        unset($attachments[$key]);
        $gradeOne->attachments = array_values($attachments);
        $gradeOne->save();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }
}
