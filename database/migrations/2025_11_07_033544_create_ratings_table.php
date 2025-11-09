<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('score'); 
            $table->timestamps();

            $table->index(['book_id', 'user_id']);
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('ratings');
    }
};
