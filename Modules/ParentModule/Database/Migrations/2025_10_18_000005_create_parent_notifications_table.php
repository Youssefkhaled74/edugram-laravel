<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->enum('notification_type', [
                'grade', 
                'attendance', 
                'payment', 
                'enrollment', 
                'certificate', 
                'quiz', 
                'assignment', 
                'announcement', 
                'message', 
                'alert'
            ]);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->integer('related_id')->nullable();
            $table->string('related_type')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('sent_via_email')->default(false);
            $table->boolean('sent_via_sms')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('sms_sent_at')->nullable();
            $table->integer('lms_id')->default(1);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('is_read');
            $table->index('notification_type');
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
        Schema::dropIfExists('parent_notifications');
    }
}

