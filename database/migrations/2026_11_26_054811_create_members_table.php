<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('role_title')->default('Member');
            $table->unsignedSmallInteger('batch')->default(2024);
            $table->unsignedInteger('points')->default(0);
            $table->unsignedTinyInteger('attendance_rate')->default(100);
            $table->unsignedInteger('contributions_count')->default(0);
            $table->unsignedInteger('competitions_count')->default(0);
            $table->string('status')->default('Active');
            $table->string('avatar_color')->default('#163CA8');
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->timestamps();

            $table->index(['status', 'points']);
            $table->index('division_id');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
