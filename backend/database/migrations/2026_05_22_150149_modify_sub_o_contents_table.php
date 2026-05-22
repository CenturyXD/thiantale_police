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
        Schema::table('sub_o_contents', function (Blueprint $table) {
            $table->renameColumn('o_content_id', 'main_sub_o_content_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_o_contents', function (Blueprint $table) {
            $table->renameColumn('main_sub_o_content_id', 'o_content_id');
        });
    }
};
