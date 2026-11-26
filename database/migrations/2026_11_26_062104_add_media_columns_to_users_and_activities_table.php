<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('locale');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->string('banner_url')->nullable()->after('accent_color');
        });
    }

    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_url');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('banner_url');
        });
    }
};
