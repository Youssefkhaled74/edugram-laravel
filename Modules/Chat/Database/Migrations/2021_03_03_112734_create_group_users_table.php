<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupUsersTable extends Migration
{
    public function up()
    {
        Schema::create('chat_group_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('group_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('role')->default(1);
            $table->unsignedBigInteger('added_by');
            $table->unsignedBigInteger('removed_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
            $table->integer('lms_id')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_group_users');
    }
}
