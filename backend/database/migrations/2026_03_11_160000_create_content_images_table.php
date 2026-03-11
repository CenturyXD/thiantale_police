<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['content_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_images');
    }
};
