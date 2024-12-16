<?php

namespace App\Http\Controllers\Api\Documentation;

/**
 * @OA\Schema(
 *     schema="GradeOne",
 *     title="Grade One",
 *     description="Grade One object schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Mathematics"),
 *     @OA\Property(property="description", type="string", example="First grade mathematics curriculum"),
 *     @OA\Property(property="subject", type="string", example="math"),
 *     @OA\Property(property="unit", type="string", example="Numbers and Operations"),
 *     @OA\Property(property="lesson", type="string", example="Addition"),
 *     @OA\Property(property="content", type="string", example="Detailed lesson content..."),
 *     @OA\Property(property="video_url", type="string", example="https://example.com/video.mp4"),
 *     @OA\Property(property="attachments", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="GradeOneRequest",
 *     title="Grade One Request",
 *     description="Grade One request schema",
 *     required={"title", "subject", "unit", "lesson", "content"},
 *     @OA\Property(property="title", type="string", example="Mathematics"),
 *     @OA\Property(property="description", type="string", example="First grade mathematics curriculum"),
 *     @OA\Property(property="subject", type="string", example="math"),
 *     @OA\Property(property="unit", type="string", example="Numbers and Operations"),
 *     @OA\Property(property="lesson", type="string", example="Addition"),
 *     @OA\Property(property="content", type="string", example="Detailed lesson content..."),
 *     @OA\Property(property="video_url", type="string", example="https://example.com/video.mp4"),
 *     @OA\Property(property="attachments", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="order", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class GradeOneSchema {}
