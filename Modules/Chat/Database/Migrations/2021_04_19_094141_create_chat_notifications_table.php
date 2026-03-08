<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('chat_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->integer('lms_id')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_notifications');
    }
}
