<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMessageRecipientsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_group_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('conversation_id');
            $table->string('group_id');
            $table->dateTime('read_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->integer('lms_id')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_group_message_recipients');
    }
}
