<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // إضافة صلاحية عرض توثيق API
        Permission::create(['name' => 'view-api-documentation']);

        // إضافة الصلاحية للمشرف
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo('view-api-documentation');
        }
    }
}
