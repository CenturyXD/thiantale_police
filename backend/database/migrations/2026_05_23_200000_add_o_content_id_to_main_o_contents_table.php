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
        Schema::table('main_o_contents', function (Blueprint $table) {
            $table->unsignedBigInteger('o_content_id')->nullable()->after('os_id');
            // ถ้าต้องการ foreign key:
            // $table->foreign('o_content_id')->references('id')->on('o_contents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('main_o_contents', function (Blueprint $table) {
            $table->dropColumn('o_content_id');
        });
    }
};
