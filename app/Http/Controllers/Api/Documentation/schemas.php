<?php

/**
 * @OA\Schema(
 *     schema="Article",
 *     required={"id", "title", "class_id", "subject_id", "semester_id"},
 *     @OA\Property(property="id", type="integer", example=1, description="Unique identifier of the article"),
 *     @OA\Property(property="title", type="string", example="Introduction to Mathematics", description="Title of the article"),
 *     @OA\Property(property="description", type="string", example="This article covers basic mathematical concepts", description="Detailed description of the article"),
 *     @OA\Property(property="class_id", type="integer", example=1, description="The class this article belongs to"),
 *     @OA\Property(property="subject_id", type="integer", example=1, description="The subject this article covers"),
 *     @OA\Property(property="semester_id", type="integer", example=1, description="The semester this article is for"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-15T10:00:00Z", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-15T10:30:00Z", description="Last update timestamp"),
 *     @OA\Property(
 *         property="keywords",
 *         type="array",
 *         description="Associated keywords/tags",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Keyword ID"),
 *             @OA\Property(property="name", type="string", example="mathematics", description="Keyword name")
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ArticleRequest",
 *     required={"class_id", "subject_id", "semester_id", "title"},
 *     @OA\Property(property="class_id", type="integer", example=1, description="The ID of the class this article belongs to"),
 *     @OA\Property(property="subject_id", type="integer", example=1, description="The ID of the subject this article covers"),
 *     @OA\Property(property="semester_id", type="integer", example=1, description="The ID of the semester this article is for"),
 *     @OA\Property(property="title", type="string", example="Introduction to Mathematics", description="Title of the article (max: 255 characters)"),
 *     @OA\Property(property="description", type="string", example="This article covers basic mathematical concepts including algebra, geometry, and arithmetic", description="Detailed description of the article content"),
 *     @OA\Property(
 *         property="keywords",
 *         type="array",
 *         description="List of keywords/tags for the article",
 *         @OA\Items(type="string"),
 *         example={"math", "basics", "algebra", "geometry"}
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     @OA\Property(property="message", type="string", example="The given data was invalid"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(property="title", type="array", @OA\Items(type="string"), example={"The title field is required"}),
 *     )
 * )
