<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Workshop');
            $table->string('status')->default('Upcoming');
            $table->date('event_date');
            $table->string('location')->nullable();
            $table->unsignedInteger('max_attendees')->default(50);
            $table->unsignedInteger('initial_attendees')->default(0);
            $table->unsignedInteger('points_reward')->default(50);
            $table->string('icon')->default('calendar');
            $table->string('accent_color')->default('#163CA8');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['status', 'event_date']);
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
