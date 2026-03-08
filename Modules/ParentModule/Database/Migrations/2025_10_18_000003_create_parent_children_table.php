<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_children', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('student_id');
            $table->enum('relationship_type', [
                'father', 
                'mother', 
                'guardian', 
                'grandfather', 
                'grandmother', 
                'uncle', 
                'aunt', 
                'sibling', 
                'other'
            ])->default('father');
            $table->boolean('is_primary_parent')->default(false);
            $table->boolean('can_make_payments')->default(true);
            $table->boolean('can_view_grades')->default(true);
            $table->boolean('can_view_attendance')->default(true);
            $table->boolean('can_communicate_teachers')->default(true);
            $table->boolean('can_enroll_courses')->default(true);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('lms_id')->default(1);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint to prevent duplicate relationships
            $table->unique(['parent_id', 'student_id'], 'parent_children_unique');
            
            // Indexes
            $table->index('lms_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parent_children');
    }
}

