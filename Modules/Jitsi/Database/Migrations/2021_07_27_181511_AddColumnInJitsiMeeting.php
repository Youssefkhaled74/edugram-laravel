<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInJitsiMeeting extends Migration
{

    public function up()
    {
        Schema::table('jitsi_meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('jitsi_meetings', 'duration')) {
                $table->integer('duration')->default(0);
            }
        });
    }


    public function down()
    {
        //
    }
}
