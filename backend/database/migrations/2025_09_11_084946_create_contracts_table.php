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
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->unique();
            $table->string('deposit')->nullable();
            $table->date('start_date');
            $table->date('end_date');

            $table->enum('status', [
                'Pending',     // Chờ duyệt
                'Approved',    // Đã duyệt, chờ bắt đầu
                'Active',      // Đang hiệu lực
                'Completed',   // Hết hạn bình thường
                'Terminated',  // Kết thúc sớm
                'Rejected'     // Từ chối
            ])->default('Pending');

            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
