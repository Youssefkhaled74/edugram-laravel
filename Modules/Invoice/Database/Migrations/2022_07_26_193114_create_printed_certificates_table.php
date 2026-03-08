<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Invoice\Entities\PrintedCertificate;

class CreatePrintedCertificatesTable extends Migration
{

    public function up()
    {
        Schema::create('printed_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->float('price')->nullable()->default(0);
            $table->timestamps();
        });
        PrintedCertificate::firstOrCreate([
            'title'=>'Printed Certificate',
            'price'=>0
        ]);
    }


    public function down()
    {
        Schema::dropIfExists('printed_certificates');
    }
}
