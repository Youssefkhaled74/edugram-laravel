<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWatchTimeSecondsToLessonCompletesTable extends Migration
{
    public function up()
    {
        Schema::table('lesson_completes', function (Blueprint $table) {
            $table->integer('watch_time_seconds')->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::table('lesson_completes', function (Blueprint $table) {
            $table->dropColumn('watch_time_seconds');
        });
    }
}
