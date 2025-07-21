<?php

namespace LaravelApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Only allow profile updates for internal authentication users
        if ($user->auth_type !== 'internal' && !empty($user->auth_type)) {
            return redirect()->back()->with('error', 'Profile cannot be edited for external authentication users. Please contact your identity provider.');
        }

        Log::info('Profile update attempt', [
            'user_id' => $user->id,
            'current_name' => $user->name,
            'current_email' => $user->email,
            'new_name' => $request->name,
            'new_email' => $request->email,
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        Log::info('Profile update result', [
            'updated_name' => $user->name,
            'updated_email' => $user->email,
        ]);

        // Check if request came from settings page
        if ($request->has('from_settings') || str_contains($request->header('referer', ''), '/settings')) {
            return redirect()->route('settings')->with('success', 'Profile updated successfully!');
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // Only allow password updates for internal authentication users
        if ($user->auth_type !== 'internal' && !empty($user->auth_type)) {
            return redirect()->back()->with('error', 'Password cannot be changed for external authentication users.');
        }
        
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Invalidate current session and regenerate session ID
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        // Logout current user
        Auth::logout();
        
        // Clear any existing session data
        request()->session()->flush();
        
        return redirect()->route('login')->with('success', 'Password updated successfully! Please login with your new password.');
    }

    /**
     * Show settings page with authentication type restrictions.
     */
    public function settings()
    {
        $user = Auth::user();
        
        // Check if user has internal authentication for settings access
        $canEditProfile = ($user->auth_type === 'internal' || empty($user->auth_type));
        
        return view('settings', compact('user', 'canEditProfile'));
    }
}
