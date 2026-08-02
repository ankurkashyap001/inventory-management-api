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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // URL friendly name (e.g. "special-deals")
            
            // Sub-categories support (Self-referencing)
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            
            // API Specific Fields
            $table->text('description')->nullable(); // cat_desc & category_description
            $table->boolean('prescription_required')->default(false);
            $table->integer('max_discount')->default(0);
            
            // Image URLs / Paths
            $table->string('image')->nullable(); // Default single image (if needed)
            $table->string('banner_image')->nullable(); // category_banner_url
            $table->string('logo_image')->nullable(); // category_logo_url
            $table->string('logo_image_web')->nullable(); // category_logo_url_web
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};