<?php

namespace App\Http\Controllers\Api\Documentation;

/**
 * @OA\Schema(
 *     schema="Article",
 *     title="Article",
 *     description="Article object schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Introduction to Mathematics"),
 *     @OA\Property(property="content", type="string", example="This article explains basic mathematics concepts."),
 *     @OA\Property(property="country", type="string", example="jordan", description="Country code (jordan, saudi, egypt, palestine)"),
 *     @OA\Property(property="author", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="email", type="string", example="john@example.com")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-06-15T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-06-15T10:00:00Z"),
 *     @OA\Property(
 *         property="keywords",
 *         type="array",
 *         @OA\Items(type="string", example="math")
 *     )
 * )
 */
class ArticleSchema {}
