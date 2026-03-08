<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCertificatesTable extends Migration
{

    public function up()
    {
        Schema::create('order_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('tracking')->nullable();
            $table->integer('checkout_id')->nullable();
            $table->integer('certificate_id')->nullable();
            $table->float('price')->nullable();
            $table->integer('course_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('status')->nullable()->default('ordered');
            $table->integer('accepted')->nullable();
            $table->tinyInteger('payment_status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_certificates');
    }
}
