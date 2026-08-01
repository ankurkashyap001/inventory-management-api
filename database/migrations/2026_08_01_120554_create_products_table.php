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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        // Foreign Key connected to Categories table
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        
        $table->string('name');
        $table->string('slug')->unique(); // e.g., 'iphone-15-pro-max'
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2); // Original Price
        $table->decimal('sale_price', 10, 2)->nullable(); // Discounted Price
        $table->integer('stock')->default(0);
        $table->string('sku')->unique(); // Stock Keeping Unit (e.g., 'PROD-001')
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
