<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelApp\Models\User;
use LaravelApp\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Assign admin role to first user (typically the system creator)
        $adminRole = Role::where('name', 'admin')->first();
        $defaultRole = Role::where('is_default', true)->first();
        
        if ($adminRole) {
            // Find the first user and assign admin role
            $firstUser = User::first();
            
            if ($firstUser) {
                $firstUser->update(['role_id' => $adminRole->id]);
                echo "Assigned admin role to first user: {$firstUser->name}" . PHP_EOL;
            }
            
            // Assign default role to all other users
            if ($defaultRole && $firstUser) {
                User::where('id', '!=', $firstUser->id)
                    ->update(['role_id' => $defaultRole->id]);
                echo "Assigned default role to remaining users" . PHP_EOL;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all users to default role
        $defaultRole = Role::where('is_default', true)->first();
        
        if ($defaultRole) {
            User::whereNotNull('role_id')->update(['role_id' => $defaultRole->id]);
        }
    }
};
