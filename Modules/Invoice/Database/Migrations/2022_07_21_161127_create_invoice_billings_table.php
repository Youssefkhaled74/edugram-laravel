<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceBillingsTable extends Migration
{

    public function up()
    {
        Schema::create('invoice_billings', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id');
            $table->unsignedBigInteger('user_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('country')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('details')->nullable();
            $table->text('payment_method')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('invoice_billings');
    }
}
