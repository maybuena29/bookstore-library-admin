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
        Schema::dropIfExists('books_tbl');
        Schema::create('books_tbl', function (Blueprint $table) {
            $table->string('book_id')->primary();
            $table->string('book_name');
            $table->string('author');
            $table->string('book_cover')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->collation = 'utf8mb4_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_tbl');
    }
};
