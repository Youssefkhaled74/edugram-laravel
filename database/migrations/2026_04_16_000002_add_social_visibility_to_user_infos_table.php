<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('user_infos', 'show_facebook')) {
                $table->boolean('show_facebook')->default(1)->after('show_experience');
            }
            if (!Schema::hasColumn('user_infos', 'show_instagram')) {
                $table->boolean('show_instagram')->default(1)->after('show_facebook');
            }
            if (!Schema::hasColumn('user_infos', 'show_whatsapp')) {
                $table->boolean('show_whatsapp')->default(1)->after('show_instagram');
            }
            if (!Schema::hasColumn('user_infos', 'show_twitter')) {
                $table->boolean('show_twitter')->default(1)->after('show_whatsapp');
            }
            if (!Schema::hasColumn('user_infos', 'show_snapchat')) {
                $table->boolean('show_snapchat')->default(1)->after('show_twitter');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            if (Schema::hasColumn('user_infos', 'show_snapchat')) {
                $table->dropColumn('show_snapchat');
            }
            if (Schema::hasColumn('user_infos', 'show_twitter')) {
                $table->dropColumn('show_twitter');
            }
            if (Schema::hasColumn('user_infos', 'show_whatsapp')) {
                $table->dropColumn('show_whatsapp');
            }
            if (Schema::hasColumn('user_infos', 'show_instagram')) {
                $table->dropColumn('show_instagram');
            }
            if (Schema::hasColumn('user_infos', 'show_facebook')) {
                $table->dropColumn('show_facebook');
            }
        });
    }
};
