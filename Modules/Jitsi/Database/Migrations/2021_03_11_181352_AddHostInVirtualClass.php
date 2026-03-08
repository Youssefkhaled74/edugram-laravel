<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddHostInVirtualClass extends Migration
{


    public function up()
    {
        DB::statement("ALTER TABLE `virtual_classes` CHANGE `host` `host` VARCHAR(50)   NULL DEFAULT 'Zoom';");

    }



    public function down()
    {
        //
    }
}
