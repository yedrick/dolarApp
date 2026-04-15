<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['compra', 'venta']);
            $table->decimal('price', 8, 4);
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['activa', 'cerrada', 'expirada'])->default('activa');
            $table->string('contact_info', 200)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index(['status', 'type']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
