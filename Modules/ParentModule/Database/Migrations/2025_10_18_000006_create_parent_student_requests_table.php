<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentStudentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_student_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->string('student_email')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_name');
            $table->date('student_dob')->nullable();
            $table->string('student_national_id')->nullable();
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
            ]);
            $table->enum('request_type', ['link_existing', 'create_new'])->default('link_existing');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('supporting_documents')->nullable()->comment('JSON array of document paths');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->integer('lms_id')->default(1);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('status');
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
        Schema::dropIfExists('parent_student_requests');
    }
}

