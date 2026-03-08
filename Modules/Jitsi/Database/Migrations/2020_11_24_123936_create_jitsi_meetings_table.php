<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJitsiMeetingsTable extends Migration
{

    public function up()
    {
        Schema::create('jitsi_meetings', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->nullable()->default(1);
            $table->integer('instructor_id')->nullable()->default(1);
            $table->integer('class_id')->nullable();
            $table->text('meeting_id')->nullable()->default(null);
            $table->text('topic')->nullable()->default(null);
            $table->text('description')->nullable();
            $table->text('date')->nullable()->default(null);
            $table->text('time')->nullable()->default(null);
            $table->text('datetime')->nullable()->default(null);
            $table->integer('lms_id')->default(1);

            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('jitsi_meetings');
    }
}
