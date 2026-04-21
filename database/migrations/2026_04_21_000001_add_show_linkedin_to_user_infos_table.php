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
        Schema::table('user_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('user_infos', 'show_linkedin')) {
                $table->boolean('show_linkedin')->default(1)->after('show_instagram');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            if (Schema::hasColumn('user_infos', 'show_linkedin')) {
                $table->dropColumn('show_linkedin');
            }
        });
    }
};
