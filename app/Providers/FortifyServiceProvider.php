<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use App\Models\LoginAttempt;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // تكوين صفحات المصادقة
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        // إضافة معالجة تسجيل الدخول مع تتبع المحاولات
        Fortify::authenticateUsing(function (Request $request) {
            // تسجيل محاولة تسجيل الدخول
            $loginAttempt = new LoginAttempt([
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'successful' => false
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $loginAttempt->failure_reason = 'User not found';
                $loginAttempt->save();
                return null;
            }

            if (!Hash::check($request->password, $user->password)) {
                $loginAttempt->failure_reason = 'Invalid password';
                $loginAttempt->save();
                return null;
            }

            // تحديث محاولة تسجيل الدخول كناجحة
            $loginAttempt->successful = true;
            $loginAttempt->failure_reason = null;
            $loginAttempt->save();

            // تجديد الجلسة
            $request->session()->regenerate();
            
            // حفظ معلومات المستخدم في الجلسة
            Session::put('user_id', $user->id);
            Session::put('user_name', $user->name);
            Session::put('user_email', $user->email);
            
            return $user;
        });

        // تكوين حد المحاولات
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower($request->input(Fortify::username()));
            
            // التحقق من عدد المحاولات الفاشلة في آخر 5 دقائق
            $recentFailedAttempts = LoginAttempt::where('email', $email)
                ->where('successful', false)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();
            
            // السماح بـ 5 محاولات فقط كل 5 دقائق
            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
