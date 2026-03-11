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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('username')->after('name');
            $table->string('phone')->after('email');
            $table->string('role')->after('phone')->default('user');
            $table->string('status')->after('role')->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
                $table->dropColumn('username');
                $table->dropColumn('phone');
                $table->dropColumn('role');
                $table->dropColumn('status');
        });
    }
};
