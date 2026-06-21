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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            // Unique URL slug
            $table->string('slug', 50)->unique();

            // Note content
            $table->longText('note')->nullable();

            // Public / Private
            $table->enum('visibility', ['public', 'private'])->default('public');

            // Password for private notes (hashed)
            $table->string('password')->nullable();

            // View counter
            $table->unsignedBigInteger('views')->default(0);

            // Optional expiry date
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('visibility');
            $table->index('expires_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
