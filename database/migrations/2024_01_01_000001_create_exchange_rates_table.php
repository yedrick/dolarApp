<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['oficial', 'paralelo', 'librecambista'])->unique();
            $table->decimal('buy_price', 8, 4);
            $table->decimal('sell_price', 8, 4);
            $table->string('source', 100)->default('manual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
