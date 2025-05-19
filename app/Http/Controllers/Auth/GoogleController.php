<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // 1) Redirect user to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account']) // optional
            ->redirect();
    }

    // 2) Handle callback from Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors('Unable to login using Google. Please try again.');
        }

        // 3) Find or create a local user
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'     => $googleUser->getName(),
                'password' => bcrypt(Str::random(16)), // random password
                // you can store $googleUser->getAvatar() if you like
            ]
        );

        // 4) Log them in
        Auth::login($user, true);

        // 5) Redirect where you want
        return redirect()->intended('/');
    }
}
