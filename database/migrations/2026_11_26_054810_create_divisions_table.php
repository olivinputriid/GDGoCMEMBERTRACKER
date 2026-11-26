<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('type')->default('curriculum');
            $table->string('color')->default('#163CA8');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
