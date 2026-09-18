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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('icon')->nullable()->after('description');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->string('icon')->nullable()->after('description');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->boolean('is_featured')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['slug', 'icon', 'is_featured']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['slug', 'icon', 'is_featured']);
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_featured']);
        });
    }
};
