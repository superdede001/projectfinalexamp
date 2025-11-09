<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique()->nullable();
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('publisher');
            $table->integer('publication_year')->nullable();
            $table->enum('availability_status', ['available', 'rented', 'reserved'])->default('available');
            $table->string('store_location')->nullable();
            $table->timestamps();

            $table->index('author_id');
            $table->index('category_id');
            $table->index('store_location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
