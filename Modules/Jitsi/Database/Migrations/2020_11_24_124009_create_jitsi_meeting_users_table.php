<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJitsiMeetingUsersTable extends Migration
{


    public function up()
    {
        Schema::create('jitsi_meeting_users', function (Blueprint $table) {
            $table->id();
            $table->integer('meeting_id')->default(1);
            $table->integer('user_id')->default(1);
            $table->integer('lms_id')->default(1);

            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('jitsi_meeting_users');
    }
}
