<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('tracking')->nullable();
            $table->string('invoice_number')->nullable();
            $table->integer('user_id');
            $table->integer('checkout_id')->nullable();
            $table->unsignedBigInteger('billing_detail_id')->nullable();
            $table->integer('package_id')->nullable();
            $table->integer('coupon_id')->nullable();
            $table->float('discount')->default(0.00);
            $table->float('purchase_price')->default(0.00);
            $table->float('price')->default(0.00);
            $table->string('status')->default('created');
            $table->string('payment_method')->nullable();
            $table->integer('payment_type')->nullable()->comment('1=online , 2=offline');
            $table->longText('response')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
