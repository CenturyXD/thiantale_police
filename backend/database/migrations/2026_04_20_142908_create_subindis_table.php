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
        Schema::create('subindis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indi_id');
            $table->string('name');
            $table->string('status');
            $table->timestamps();

            $table->foreign('indi_id')->references('id')->on('indis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subindis');
    }
};
