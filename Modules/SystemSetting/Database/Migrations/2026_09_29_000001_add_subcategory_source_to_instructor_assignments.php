<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('instructor_subcategories') || Schema::hasColumn('instructor_subcategories', 'subcategory_type')) {
            return;
        }

        Schema::table('instructor_subcategories', function (Blueprint $table) {
            $table->dropPrimary();
            $table->string('subcategory_type', 20)->nullable();
        });

        $assignments = DB::table('instructor_subcategories')->get();
        foreach ($assignments as $assignment) {
            $source = Schema::hasTable('sub_categories')
                && DB::table('sub_categories')->where('id', $assignment->subcategory_id)->exists()
                ? 'sub_category'
                : 'category';

            DB::table('instructor_subcategories')
                ->where('user_id', $assignment->user_id)
                ->where('subcategory_id', $assignment->subcategory_id)
                ->update(['subcategory_type' => $source]);
        }

        Schema::table('instructor_subcategories', function (Blueprint $table) {
            $table->primary(['user_id', 'subcategory_type', 'subcategory_id']);
        });
    }

    public function down()
    {
        if (!Schema::hasTable('instructor_subcategories') || !Schema::hasColumn('instructor_subcategories', 'subcategory_type')) {
            return;
        }

        $hasDuplicateIds = DB::table('instructor_subcategories')
            ->select('user_id', 'subcategory_id')
            ->groupBy('user_id', 'subcategory_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();
        $hasAmbiguousCategoryIds = Schema::hasTable('sub_categories')
            && DB::table('instructor_subcategories as assignments')
                ->join('sub_categories', 'sub_categories.id', '=', 'assignments.subcategory_id')
                ->where('assignments.subcategory_type', 'category')
                ->exists();

        if ($hasDuplicateIds || $hasAmbiguousCategoryIds) {
            return;
        }

        Schema::table('instructor_subcategories', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('subcategory_type');
            $table->primary(['user_id', 'subcategory_id']);
        });
    }
};
