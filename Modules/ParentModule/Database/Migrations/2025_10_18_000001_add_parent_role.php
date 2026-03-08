<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddParentRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add Parent role to roles table
        DB::table('roles')->insert([
            'name' => 'Parent',
            'type' => 'System',
            'details' => 'Parent/Guardian role for managing children education',
            'created_at' => now(),
            'updated_at' => now(),
            'lms_id' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->where('name', 'Parent')->where('type', 'System')->delete();
    }
}

