<?php

namespace App\Http\Controllers\Api\Documentation;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User object schema",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role", type="string", enum={"admin", "editor", "user"}, example="user"),
 *     @OA\Property(property="avatar", type="string", example="users/avatar-1.jpg"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="job_title", type="string", example="Software Engineer"),
 *     @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
 *     @OA\Property(property="country", type="string", example="United States"),
 *     @OA\Property(property="bio", type="string", example="A passionate developer..."),
 *     @OA\Property(
 *         property="social_links",
 *         type="object",
 *         example={"twitter": "https://twitter.com/johndoe", "linkedin": "https://linkedin.com/in/johndoe"}
 *     ),
 *     @OA\Property(property="is_online", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="UserRequest",
 *     title="User Request",
 *     description="User request schema",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="secret123"),
 *     @OA\Property(property="role", type="string", enum={"admin", "editor", "user"}, example="user"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="job_title", type="string", example="Software Engineer"),
 *     @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
 *     @OA\Property(property="country", type="string", example="United States"),
 *     @OA\Property(property="bio", type="string", example="A passionate developer..."),
 *     @OA\Property(
 *         property="social_links",
 *         type="object",
 *         example={"twitter": "https://twitter.com/johndoe", "linkedin": "https://linkedin.com/in/johndoe"}
 *     )
 * )
 */
class UserSchema {}
