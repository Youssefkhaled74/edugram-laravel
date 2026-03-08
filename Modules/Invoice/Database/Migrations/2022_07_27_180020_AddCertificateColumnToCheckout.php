<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCertificateColumnToCheckout extends Migration
{

    public function up()
    {
        Schema::table('checkouts', function (Blueprint $table) {
            if (!Schema::hasColumn('checkouts', 'order_certificate_id')) {
                $table->string('order_certificate_id')->nullable();
            }
        });
    }

    public function down()
    {
     }
}
