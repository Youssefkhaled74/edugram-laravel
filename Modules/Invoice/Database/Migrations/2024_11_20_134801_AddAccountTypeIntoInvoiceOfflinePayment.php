<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountTypeIntoInvoiceOfflinePayment extends Migration
{

    public function up()
    {

         Schema::table('invoice_offline_payments', function (Blueprint $table) {
             if (!Schema::hasColumn('invoice_offline_payments', 'account_type')) {
                 $table->string('account_type')->nullable();
             }
         });
    }

    public function down()
    {
        //
    }
}
