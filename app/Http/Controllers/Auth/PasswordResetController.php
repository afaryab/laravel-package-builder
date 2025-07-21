<?php

namespace LaravelApp\Http\Controllers\Auth;

use LaravelApp\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset request form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle a password reset request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // For simplicity, instead of sending email, we'll generate a temporary password
        $user = \LaravelApp\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Check if user has internal authentication
        if ($user->auth_type !== 'internal' && !empty($user->auth_type)) {
            return back()->withErrors(['email' => 'Password reset is not available for external authentication users.']);
        }

        // Generate a temporary password
        $temporaryPassword = 'Reset' . rand(100000, 999999) . '!';
        
        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        return back()->with('status', "A temporary password has been generated: {$temporaryPassword}. Please login with this password and change it immediately.");
    }

    /**
     * Display the password reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle the password reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
