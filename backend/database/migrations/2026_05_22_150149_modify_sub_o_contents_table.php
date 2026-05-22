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
        //
         Schema::table('sub_o_contents', function (Blueprint $table) {
            $table->string('url')->nullable()->after('updated_at'); // เพิ่มคอลัมน์ url หลัง updated_at
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
            Schema::table('sub_o_contents', function (Blueprint $table) {
                $table->dropColumn('url'); 
            });
    }
};
