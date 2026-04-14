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
        Schema::create('sentiment_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('score');
            $table->foreignId('ticker_id')->constrained('tickers');
            $table->timestamp('timestamp')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentiment_scores');
    }
};
