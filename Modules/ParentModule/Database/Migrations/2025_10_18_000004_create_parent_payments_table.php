<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->unsignedInteger('enrollment_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_method', 50);
            $table->string('payment_gateway', 50)->nullable();
            $table->enum('payment_status', [
                'pending', 
                'processing', 
                'completed', 
                'failed', 
                'refunded', 
                'cancelled'
            ])->default('pending');
            $table->string('transaction_id')->nullable();
            $table->text('gateway_response')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_path')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0.00);
            $table->timestamp('refund_date')->nullable();
            $table->text('refund_reason')->nullable();
            $table->integer('lms_id')->default(1);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('parent_id')->references('id')->on('parents')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('enrollment_id')->references('id')->on('course_enrolleds')->onDelete('set null');
            
            // Indexes
            $table->index('course_id');
            $table->index('payment_status');
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
        Schema::dropIfExists('parent_payments');
    }
}

