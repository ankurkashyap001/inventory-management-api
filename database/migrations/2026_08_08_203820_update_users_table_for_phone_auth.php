<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            // Add phone column only if it doesn't exist yet, otherwise alter it
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->unique()->after('id')->nullable();
            } else {
                $table->string('phone')->unique()->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};