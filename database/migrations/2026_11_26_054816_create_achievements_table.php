<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('trophy');
            $table->string('description')->nullable();
            $table->string('bg_color')->default('#EEF2FF');
            $table->string('text_color')->default('#163CA8');
            $table->unsignedInteger('min_points')->nullable();
            $table->timestamps();
        });

        Schema::create('achievement_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained('achievements')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->timestamp('awarded_at')->nullable();
            $table->timestamps();

            $table->unique(['achievement_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_member');
        Schema::dropIfExists('achievements');
    }
};
