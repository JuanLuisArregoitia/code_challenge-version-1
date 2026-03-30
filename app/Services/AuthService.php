<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }

    public function login(Request $request, array $credentials): ?User
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return Auth::user();
    }

    public function logout(Request $request): void
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    public function sendResetLink(array $data): string
    {
        return Password::sendResetLink($data);
    }

    public function resetPassword(array $data): string
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    }

    public function verifyEmail(int $id, string $hash): array
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return ['success' => false, 'message' => 'Invalid verification link.'];
        }

        if ($user->hasVerifiedEmail()) {
            return ['success' => true, 'message' => 'Email already verified.'];
        }

        $user->markEmailAsVerified();

        return ['success' => true, 'message' => 'Email verified successfully.'];
    }

    public function resendVerificationEmail(User $user): array
    {
        if ($user->hasVerifiedEmail()) {
            return ['already_verified' => true, 'message' => 'Email already verified.'];
        }

        $user->sendEmailVerificationNotification();

        return ['already_verified' => false, 'message' => 'Verification link sent.'];
    }
}
