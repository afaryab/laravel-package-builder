<?php

namespace Tests\Feature;

use Tests\TestCase;
use LaravelApp\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_registration_form_when_no_users_exist()
    {
        // Set auth type to internal
        config(['auth.type' => 'internal']);
        
        // Ensure no users exist
        $this->assertEquals(0, User::count());
        
        // Visit login page - should show registration form
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertSee('Setup Admin Account');
        $response->assertSee('Create the first admin user for this application');
        $response->assertSee('Create Admin Account');
    }

    public function test_shows_login_form_when_users_exist()
    {
        // Set auth type to internal
        config(['auth.type' => 'internal']);
        
        // Create a user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'auth_type' => 'internal',
        ]);
        
        // Ensure user exists
        $this->assertEquals(1, User::count());
        
        // Visit login page - should show login form
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertSee('Login');
        $response->assertSee('Sign in to your account');
        $response->assertSee('Sign In');
    }

    public function test_can_register_first_user()
    {
        // Set auth type to internal
        config(['auth.type' => 'internal']);
        
        // Ensure no users exist
        $this->assertEquals(0, User::count());
        
        // Submit registration form
        $response = $this->post('/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        // Should redirect to home
        $response->assertRedirect('/');
        
        // User should be created
        $this->assertEquals(1, User::count());
        
        // User should be authenticated
        $this->assertAuthenticated();
        
        // Check user details
        $user = User::first();
        $this->assertEquals('Admin User', $user->name);
        $this->assertEquals('admin@example.com', $user->email);
        $this->assertEquals('internal', $user->auth_type);
    }

    public function test_registration_blocked_when_users_exist()
    {
        // Set auth type to internal
        config(['auth.type' => 'internal']);
        
        // Create a user
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password'),
            'auth_type' => 'internal',
        ]);
        
        // Try to register another user
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        // Should return 403 forbidden
        $response->assertStatus(403);
        
        // Should still only have one user
        $this->assertEquals(1, User::count());
    }
}
