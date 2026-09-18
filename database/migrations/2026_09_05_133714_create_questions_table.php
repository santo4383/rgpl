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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained()->onDelete('set null');

            $table->string('question_type')->default('STANDARD_MCQ');
            $table->string('language')->default('en');

            $table->text('question_text')->nullable();
            $table->text('question_html')->nullable();

            $table->text('explanation_text')->nullable();
            $table->text('explanation_html')->nullable();

            $table->string('difficulty')->default('Medium');

            $table->string('source_type')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_year')->nullable();
            $table->string('source_reference')->nullable();

            $table->string('status')->default('DRAFT'); // DRAFT, REVIEW, PUBLISHED, ARCHIVED

            $table->boolean('is_free')->default(true);
            $table->boolean('is_premium')->default(false);

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
