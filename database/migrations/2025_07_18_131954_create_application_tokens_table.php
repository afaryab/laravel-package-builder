<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('application_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Token name/description
            $table->string('token', 80)->unique(); // Hashed token
            $table->string('type')->default('integration'); // 'integration' or 'user'
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // For user tokens
            $table->string('external_user_id')->nullable(); // External IdP user ID
            $table->string('external_provider')->nullable(); // 'authentik', 'saml', 'oauth'
            $table->json('scopes')->nullable(); // Token permissions
            $table->json('metadata')->nullable(); // Additional data
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['token', 'type']);
            $table->index(['user_id', 'type']);
            $table->index(['external_user_id', 'external_provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_tokens');
    }
};
