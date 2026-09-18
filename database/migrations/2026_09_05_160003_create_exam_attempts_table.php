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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->string('status', 30)->default('IN_PROGRESS'); // IN_PROGRESS, SUBMITTED, TIMED_OUT, ABANDONED
            $table->integer('total_questions')->default(0);
            $table->integer('attempted_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('incorrect_answers')->default(0);
            $table->integer('unanswered_questions')->default(0);
            $table->decimal('score_obtained', 8, 2)->default(0.00);
            $table->decimal('total_marks', 8, 2)->default(0.00);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('is_passed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'exam_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
