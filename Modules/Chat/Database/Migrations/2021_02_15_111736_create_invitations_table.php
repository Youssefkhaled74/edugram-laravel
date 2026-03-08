<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_invitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->tinyInteger('status')->default(0)
                ->comment('0- pending, 1- connected, 2- blocked');
            $table->timestamps();
            $table->integer('lms_id')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_invitations');
    }
}
