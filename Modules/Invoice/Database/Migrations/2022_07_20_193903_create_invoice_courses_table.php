<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceCoursesTable extends Migration
{

    public function up()
    {
        Schema::create('invoice_courses', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable();
            $table->string('tracking')->nullable();
            $table->integer('course_id')->nullable();
            $table->integer('instructor_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('price')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('invoice_courses');
    }
}
