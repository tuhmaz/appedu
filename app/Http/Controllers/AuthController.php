<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // POST [ name, email, password ]
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => []
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // User object
        $user = User::where("email", $request->email)->first();

        if (!empty($user)) {
            // User exists
            if (Hash::check($request->password, $user->password)) {
                // Password matched
                $token = $user->createToken("myAccessToken")->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successful",
                    "token" => $token,
                    "data" => [
                        'user' => $user // قم بإضافة بيانات المستخدم هنا
                    ]
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Password didn't match",
                    "data" => []
                ]);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Invalid Email value",
                "data" => []
            ]);
        }
    }

    public function profile()
    {
        $userData = auth()->user(); // تأكد من أن هذا الاستدعاء يتم بشكل صحيح

        return response()->json([
            "status" => true,
            "message" => "Profile information",
            "data" => $userData,
            "id" => auth()->user()->id // ربما تسبب في الخطأ إذا كان `auth()` لم يكن معرفًا بشكل صحيح
        ]);
    }

    public function logout()
    {
        // تحديث حالة المستخدم إلى offline
        $user = auth()->user();
        if ($user) {
            $user->status = 'offline';
            $user->last_seen_at = now();
            $user->save();
        }

        // حذف التوكن الحالي
        $token = auth()->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        // تنظيف الجلسة والكوكيز
        auth()->guard('web')->logout();
        session()->flush();
        session()->regenerate();

        // حذف كوكيز تذكرني
        if (isset($_COOKIE['remember_web'])) {
            setcookie('remember_web', '', time() - 3600, '/');
        }

        return response()->json([
            "status" => true,
            "message" => "تم تسجيل الخروج بنجاح"
        ]);
    }

    /**
     * Handle mobile app login
     */
    public function mobileLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        // Create token for mobile app
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Handle mobile app registration
     */
    public function mobileRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'device_name' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Get user profile
     */
    public function mobileProfile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user(),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user = $request->user();

        if ($request->has('current_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The provided current password is incorrect.',
                ], 401);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }
}
