<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

            $table->integer('target_pages')->comment('目標読書ページ数');
            $table->date('target_date')->comment('目標達成日');
            $table->string('status')->default('in_progress')->comment('ステータス');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_plans');
    }
};
