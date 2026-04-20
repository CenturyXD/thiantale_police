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
        Schema::create('o_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('os_id'); // ความสัมพันธ์กับตาราง os
            $table->string('title')->nullable(); // หัวข้อ
            $table->text('content')->nullable(); // เนื้อหา
            $table->string('section'); // หน้าที่แสดง: news, personnel, announcement, e-service, statistics, gallery, documents, qa
            $table->string('image')->nullable(); // รูปภาพ
            $table->string('file_url')->nullable(); // ไฟล์แนบ (PDF, DOC, etc.)
            $table->enum('status', ['draft', 'published'])->default('draft'); // สถานะ
            $table->date('publish_date')->nullable(); // วันที่เผยแพร่
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade'); // ผู้เขียน
            $table->integer('view_count')->default(0); // จำนวนการดู
            $table->integer('order')->default(0); // ลำดับการแสดง
            $table->timestamps();

            // Index
            $table->index('section');
            $table->index('status');
            $table->index('publish_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_contents');
    }
};
