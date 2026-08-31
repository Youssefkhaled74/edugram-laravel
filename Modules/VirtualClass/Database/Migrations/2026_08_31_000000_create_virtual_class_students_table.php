<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualClassStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('virtual_class_students', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('virtual_class_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->unique(['virtual_class_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('virtual_class_students');
    }
}
