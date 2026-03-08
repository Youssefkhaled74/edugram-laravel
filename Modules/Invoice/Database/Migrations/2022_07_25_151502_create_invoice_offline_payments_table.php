<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceOfflinePaymentsTable extends Migration
{

    public function up()
    {
        Schema::create('invoice_offline_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_id')->nullable()->unsigned();
            $table->foreign('invoice_id')->references('id')->on('invoices')
            ->onDelete('cascade');
            $table->integer('checkout_id');
            $table->integer('user_id');
            $table->text('bank_name')->nullable();
            $table->text('account_holder')->nullable();
            $table->text('branch_name')->nullable();
            $table->text('amount')->nullable();
            $table->text('account_number')->nullable();
            $table->text('image')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('accept_or_reject')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_offline_payments');
    }
}
