<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Notifications\RoleAssigned;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index(Request $request)
    {
      $users = DB::table('users')->get();
         $roles = Role::all();

         $users = User::with('roles')
            ->when($request->get('role'), function ($query) use ($request) {
                $query->whereHas('roles', function ($query) use ($request) {
                    $query->where('name', $request->get('role'));
                });
            })
            ->when($request->get('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->get('search') . '%')
                          ->orWhere('email', 'like', '%' . $request->get('search') . '%');
                });
            })
            ->paginate(10);

        return view('dashboard.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('dashboard.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));
    }



    public function permissions_roles(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('dashboard.users.permissions-roles', compact('user', 'roles', 'permissions'));
    }




    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'job_title' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female',
            'country' => 'nullable|string|max:100',
            'facebook_username' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // تحديث البيانات الأساسية
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'gender' => $request->gender,
                'country' => $request->country,
                'social_links' => $request->facebook_username ? 'https://facebook.com/' . $request->facebook_username : null,
                'bio' => $request->bio,
            ];

            // معالجة الصورة الشخصية
            if ($request->hasFile('profile_photo')) {
                // التأكد من وجود المجلد
                if (!Storage::disk('public')->exists('profile_photos')) {
                    Storage::disk('public')->makeDirectory('profile_photos');
                }

                // حذف الصورة القديمة إذا وجدت
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // تخزين الصورة الجديدة
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                if (!$path) {
                    throw new \Exception('Failed to store profile photo');
                }
                $userData['profile_photo_path'] = $path;
            }

            // تحديث بيانات المستخدم
            $user->update($userData);

            return redirect()->route('users.index')->with('success', 'User information updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }



    public function show(User $user)
{
    return view('dashboard.users.show', compact('user'));
}



    public function updatePermissionsRoles(Request $request, User $user)
    {
        // التحقق من الحقول التي يمكن أن تكون موجودة أو لا
        $request->validate([
            'roles' => 'sometimes|array',
            'permissions' => 'sometimes|array',
        ]);

        // الحصول على الأدوار والصلاحيات الحالية للمستخدم
        $currentRoles = $user->roles->pluck('name')->toArray();
        $currentPermissions = $user->permissions->pluck('name')->toArray();

        // مزامنة الأدوار
        $newRoles = $request->roles ?? [];
        $user->syncRoles($newRoles);

        // مزامنة الصلاحيات
        $newPermissions = $request->permissions ?? [];
        $user->syncPermissions($newPermissions);

        // تسجيل الأنشطة وإرسال الإشعارات للأدوار الجديدة والمزالة
        $this->logAndNotifyRoleChanges($user, $currentRoles, $newRoles);

        // تسجيل الأنشطة وإرسال الإشعارات للصلاحيات الجديدة والمزالة
        $this->logAndNotifyPermissionChanges($user, $currentPermissions, $newPermissions);

        return redirect()->route('users.index')->with('success', 'User roles and permissions updated successfully.');
    }



    private function logAndNotifyRoleChanges(User $user, array $currentRoles, array $newRoles)
    {
        $removedRoles = array_diff($currentRoles, $newRoles);
        $addedRoles = array_diff($newRoles, $currentRoles);

        foreach ($removedRoles as $role) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log("Removed role '{$role}' from user '{$user->name}'");

            $user->notify(new RoleAssigned($role, null, 'removed'));
            Log::info("Notification sent for role {$role} removed from user {$user->name}");
        }

        foreach ($addedRoles as $role) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log("Assigned role '{$role}' to user '{$user->name}'");

            $user->notify(new RoleAssigned($role, null, 'assigned'));
            Log::info("Notification sent for role {$role} assigned to user {$user->name}");
        }
    }

    private function logAndNotifyPermissionChanges(User $user, array $currentPermissions, array $newPermissions)
    {
        $removedPermissions = array_diff($currentPermissions, $newPermissions);
        $addedPermissions = array_diff($newPermissions, $currentPermissions);

        foreach ($removedPermissions as $permission) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log("Removed permission '{$permission}' from user '{$user->name}'");

            $user->notify(new RoleAssigned(null, $permission, 'removed'));
            Log::info("Notification sent for permission {$permission} removed from user {$user->name}");
        }

        foreach ($addedPermissions as $permission) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log("Assigned permission '{$permission}' to user '{$user->name}'");

            $user->notify(new RoleAssigned(null, $permission, 'assigned'));
            Log::info("Notification sent for permission {$permission} assigned to user {$user->name}");
        }
    }

    /**
     * تحديث الصورة الشخصية للمستخدم
     */
    public function updateProfilePhoto(Request $request, User $user)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            \Log::info('Starting profile photo update for user: ' . $user->id);
            
            // التأكد من وجود المجلد
            if (!Storage::disk('public')->exists('profile_photos')) {
                Storage::disk('public')->makeDirectory('profile_photos');
            }

            // حذف الصورة القديمة إذا وجدت
            if ($user->profile_photo_path) {
                \Log::info('Deleting old photo: ' . $user->profile_photo_path);
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // تخزين الصورة الجديدة
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            \Log::info('New photo stored at: ' . $path);
            
            if (!$path) {
                throw new \Exception('Failed to store profile photo');
            }

            // تحديث مسار الصورة في قاعدة البيانات
            $updated = $user->update(['profile_photo_path' => $path]);
            \Log::info('Database update result: ' . ($updated ? 'success' : 'failed'));

            if (!$updated) {
                throw new \Exception('Failed to update database record');
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'path' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile photo update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف المستخدم المحدد من قاعدة البيانات
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            // حذف الصورة الشخصية إذا وجدت
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // حذف المستخدم
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', __('User deleted successfully'));
        } catch (\Exception $e) {
            return redirect()
                ->route('users.index')
                ->with('error', __('Failed to delete user: ') . $e->getMessage());
        }
    }

}
