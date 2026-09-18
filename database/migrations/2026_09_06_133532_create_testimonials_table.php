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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('field')->nullable(); // e.g. MPSC Aspirant, Police Bharti, 12th Commerce, CA
            $table->text('content');
            $table->string('photo')->nullable();
            $table->integer('rating')->default(5); // 1-5
            $table->string('status')->default('DRAFT'); // DRAFT, APPROVED, PUBLISHED, ARCHIVED
            $table->boolean('featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
