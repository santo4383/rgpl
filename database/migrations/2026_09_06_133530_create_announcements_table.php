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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->text('content')->nullable();
            $table->string('link')->nullable();
            $table->string('button_text')->nullable()->default('View Details');
            $table->string('priority')->default('MEDIUM'); // LOW, MEDIUM, HIGH, URGENT
            $table->string('category')->default('Exam Update'); // Exam Update, New Course, Practice Update, Important Notice
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PUBLISHED, SCHEDULED, EXPIRED, ARCHIVED
            $table->boolean('featured')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
