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
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('content_html')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('category')->default('General'); // Exam Update, New Course, Practice Update, Important Notice, General
            $table->string('author')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, REVIEW, PUBLISHED, SCHEDULED, ARCHIVED
            $table->boolean('featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
