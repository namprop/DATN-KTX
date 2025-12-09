<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); // PK
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('room_id'); // FK
            $table->string('payment_code')->unique(); // unique code
            $table->double('electricity_usage')->default(0);
            $table->double('water_usage')->default(0);
            $table->double('total_amount')->default(0);
            $table->enum('payment_status', ['unpaid', 'paid', 'refund_pending', 'refunded'])->default('unpaid');
            $table->string('description')->nullable(); // 👈 Mô tả chi tiết (vd: "Tiền điện nước tháng 10/2025")
            $table->string('month');
            $table->string('year');
            $table->timestamp('payment_date')->nullable();
            $table->timestamps(); // created_at & updated_at

            // khóa ngoại
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
