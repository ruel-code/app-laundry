<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('free_kg', 10, 2)->default(0.00)->comment('Berat gratis yang diberikan');
            $table->decimal('total_accumulated', 10, 2)->default(0.00)->comment('Total akumulasi setelah pesanan ini');
            $table->timestamps();

            $table->index('santri_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_logs');
    }
};
