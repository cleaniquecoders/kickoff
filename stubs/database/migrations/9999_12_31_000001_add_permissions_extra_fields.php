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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('module')->nullable();
            $table->string('function')->nullable();
            $table->boolean('is_enabled')->default(true);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('display_name');
            $table->boolean('is_enabled')->default(true);
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['module', 'function', 'is_enabled']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'description', 'is_enabled']);
        });
    }
};
