<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
      $googleUser = Socialite::driver('google')->stateless()->user();

     $user = User::where('email', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();

     if (!$user) {
        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt('password'),
        ]);
    } else {
         if (is_null($user->google_id)) {
            $user->update([
                'google_id' => $googleUser->getId(),
            ]);
        }
    }

     Auth::login($user);

     return redirect()->intended('/home');
    }
}
