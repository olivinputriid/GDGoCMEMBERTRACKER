<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('activities')->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('Contribution');
            $table->integer('points');
            $table->date('awarded_at');
            $table->timestamps();

            $table->index(['member_id', 'awarded_at']);
            $table->index('awarded_at');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('point_logs');
    }
};
