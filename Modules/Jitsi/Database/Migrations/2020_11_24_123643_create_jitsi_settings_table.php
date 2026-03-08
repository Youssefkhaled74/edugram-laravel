<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Jitsi\Entities\JitsiSetting;

class CreateJitsiSettingsTable extends Migration
{

    public function up()
    {
        Schema::create('jitsi_settings', function (Blueprint $table) {
            $table->id();
            $table->string('jitsi_server')->default('https://meet.jit.si/');
            $table->integer('lms_id')->default(1);
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('jitsi_settings')->insert([
            'jitsi_server' => 'https://meet.jit.si/'
        ]);
    }


    public function down()
    {
        Schema::dropIfExists('jitsi_settings');
    }
}
