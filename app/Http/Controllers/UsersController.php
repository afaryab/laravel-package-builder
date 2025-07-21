<?php

namespace LaravelApp\Http\Controllers;

use Illuminate\Http\Request;
use LaravelApp\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use LaravelApp\Services\NotificationService;

class UsersController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('auth_type', 'like', "%{$search}%");
            });
        }

        // Filter by auth type
        if ($request->has('auth_type') && !empty($request->auth_type)) {
            $query->where('auth_type', $request->auth_type);
        }

        // Sort functionality
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['name', 'email', 'auth_type', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('settings.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('settings.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'auth_type' => ['required', 'in:internal,oauth,saml'],
            'external_id' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'auth_type' => $request->auth_type,
            'external_id' => $request->external_id,
        ]);

        // Send notification to the current user about user creation
        $this->notificationService->userCreated(Auth::user(), $user);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('settings.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('settings.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'auth_type' => ['required', 'in:internal,oauth,saml'],
            'external_id' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'auth_type' => $request->auth_type,
            'external_id' => $request->external_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Send notification about password change
        $this->notificationService->passwordChanged($user, Auth::user());

        // If the user is changing their own password, logout and destroy all sessions
        if ($user->id === Auth::id()) {
            // Invalidate current session and regenerate session ID
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            
            // Logout current user
            Auth::logout();
            
            // Clear any existing session data
            request()->session()->flush();
            
            return redirect()->route('login')->with('success', 'Password updated successfully! Please login with your new password.');
        }

        return redirect()->route('settings.users.show', $user)->with('success', 'Password updated successfully!');
    }

    /**
     * Reset the user's password to a temporary password.
     */
    public function resetPassword(User $user)
    {
        // Generate a temporary password
        $temporaryPassword = 'Temp' . rand(100000, 999999) . '!';
        
        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        // Send notification about password reset
        $this->notificationService->passwordReset($user, $temporaryPassword, Auth::user());

        // If the user is resetting their own password, logout and destroy all sessions
        if ($user->id === Auth::id()) {
            // Invalidate current session and regenerate session ID
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            
            // Logout current user
            Auth::logout();
            
            // Clear any existing session data
            request()->session()->flush();
            
            return redirect()->route('login')->with('success', "Password reset successfully! Your temporary password is: {$temporaryPassword}. Please login and change it immediately.");
        }

        return redirect()->route('settings.users.show', $user)->with('success', "Password reset successfully! Temporary password: {$temporaryPassword}. Please provide this to the user and ask them to change it immediately.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deletion of current user
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
