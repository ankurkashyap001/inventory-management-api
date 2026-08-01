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
    Schema::create('product_images', function (Blueprint $table) {
        $table->id();
        // Foreign Key connected to Products table
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->string('image_path'); // File path or URL
        $table->boolean('is_primary')->default(false); // Main image for thumbnail/card view
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
