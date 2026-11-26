<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('status')->default('Present');
            $table->date('recorded_at');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'activity_id']);
            $table->index(['activity_id', 'status']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
