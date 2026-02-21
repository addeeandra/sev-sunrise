<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('author_id');
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('authors');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
