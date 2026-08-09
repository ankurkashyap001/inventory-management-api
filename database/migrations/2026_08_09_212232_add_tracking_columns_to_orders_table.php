<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Update status enum or string
            $table->string('status')->default('placed')->change();
            
            // Add tracking columns
            $table->timestamp('estimated_delivery_time')->nullable()->after('status');
            $table->string('delivery_partner_name')->nullable()->after('estimated_delivery_time');
            $table->string('delivery_partner_phone')->nullable()->after('delivery_partner_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['estimated_delivery_time', 'delivery_partner_name', 'delivery_partner_phone']);
        });
    }
};