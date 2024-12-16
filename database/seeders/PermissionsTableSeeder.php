<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $timestamp = Carbon::now();

        $permissions = [
            'manage roles',
            'manage permissions',
            'manage classes',
            'manage subjects',
            'manage semesters',
            'manage articles',
            'manage news',
            'manage users',
            'manage settings',
            'manage files',
            'manage comments',
            'manage keywords',
            'manage events',
            'manage sitemap'
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }
}
