<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();
            if ($user) {
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'given_name' => $googleUser->user['given_name'] ?? null,
                        'family_name' => $googleUser->user['family_name'] ?? null,
                    ]);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'given_name' => $googleUser->user['given_name'] ?? null,
                    'family_name' => $googleUser->user['family_name'] ?? null,
                    'password' => \Illuminate\Support\Facades\Hash::make($googleUser->name),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('cliente');
            }
            Auth::login($user, true);
            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al autenticar con Google');
        }
    }
}