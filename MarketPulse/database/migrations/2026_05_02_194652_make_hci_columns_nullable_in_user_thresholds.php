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
        Schema::table('user_thresholds', function (Blueprint $table) {
            $table->integer('hci_high')->nullable()->change();
            $table->integer('hci_low')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_thresholds', function (Blueprint $table) {
            $table->integer('hci_high')->nullable(false)->change();
            $table->integer('hci_low')->nullable(false)->change();
        });
    }
};
