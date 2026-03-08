<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlockUsersTable extends Migration
{
    public function up()
    {
        Schema::create('chat_block_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_by');
            $table->unsignedBigInteger('block_to');
            $table->timestamps();
            $table->integer('lms_id')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_block_users');
    }
}
