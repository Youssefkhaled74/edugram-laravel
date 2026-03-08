<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Invoice\Entities\InvoiceSetting;

class CreateInvoiceSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->text('footer_text')->nullable();
            $table->string('prefix')->nullable();
            $table->timestamps();
        });
        $setting = new InvoiceSetting();
        $setting->prefix = 'INFIX';
        $setting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_settings');
    }
}
