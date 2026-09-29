<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'academic_year')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('academic_year', 20)->nullable();
            });
        }

        if (!Schema::hasTable('instructor_semesters')) {
            Schema::create('instructor_semesters', function (Blueprint $table) {
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('category_id');
                $table->primary(['user_id', 'category_id']);
            });
        }

        if (!Schema::hasTable('instructor_subcategories')) {
            Schema::create('instructor_subcategories', function (Blueprint $table) {
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('subcategory_id');
                $table->primary(['user_id', 'subcategory_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('instructor_subcategories');
        Schema::dropIfExists('instructor_semesters');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'academic_year')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('academic_year');
            });
        }
    }
};