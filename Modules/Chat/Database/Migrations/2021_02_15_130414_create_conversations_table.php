<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_id')->nullable();
            $table->unsignedBigInteger('to_id')->nullable();
            $table->text('message')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 for unread,1 for seen');
            $table->tinyInteger('message_type')->default(0)->comment('0- text message, 1- image, 2- pdf, 3- doc, 4- voice');
            $table->text('file_name')->nullable();
            $table->text('original_file_name')->nullable();
            $table->boolean('initial')->default(0);
            $table->unsignedBigInteger('reply')->nullable();
            $table->unsignedBigInteger('forward')->nullable();
            $table->boolean('deleted_by_to')->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->integer('lms_id')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_invitations');
    }
}
