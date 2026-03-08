<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMessageRemovesTable extends Migration
{
    public function up()
    {
        Schema::create('chat_group_message_removes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_message_recipient_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->integer('lms_id')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_group_message_removes');
    }
}
