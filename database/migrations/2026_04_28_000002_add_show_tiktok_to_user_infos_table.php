<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->boolean('show_tiktok')->default(1)->after('show_twitter');
        });
    }

    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn('show_tiktok');
        });
    }
};
