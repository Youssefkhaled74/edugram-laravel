<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToCheckoutInvoice extends Migration
{

    public function up()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            if (!Schema::hasColumn('checkouts', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('checkouts', 'invoice_id')) {
                $table->string('invoice_id')->nullable();
            }
            if (!Schema::hasColumn('checkouts', 'payment_type')) {
                $table->integer('payment_type')->nullable()->comment('1=online , 2=offline');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'type')) {
                $table->string('type')->nullable();
            }
        });
    }

    public function down()
    {

    }
}
