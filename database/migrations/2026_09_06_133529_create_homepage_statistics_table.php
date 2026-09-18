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
        Schema::create('homepage_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('value')->nullable(); // manual override value, e.g. "10,000+"
            $table->string('stat_key')->nullable(); // e.g. "total_questions", "total_students", "free_questions", "total_courses", "mock_exams"
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->string('status')->default('ACTIVE'); // ACTIVE, INACTIVE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_statistics');
    }
};
