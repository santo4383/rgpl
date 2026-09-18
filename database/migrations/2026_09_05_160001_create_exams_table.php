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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->decimal('total_marks', 8, 2)->default(100.00);
            $table->decimal('pass_percentage', 5, 2)->default(40.00);
            $table->boolean('has_negative_marking')->default(false);
            $table->decimal('default_negative_marks', 5, 2)->default(0.00);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('course_id');
            $table->index('is_published');
            $table->index('is_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
