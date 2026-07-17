<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('weight_kg', 10, 2)->comment('Berat sebelum diskon');
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->json('item_details')->nullable()->comment('Rincian pakaian [{nama, jumlah}]');
            $table->enum('status', ['dicuci', 'dijemur', 'dilipat', 'dikemas', 'selesai'])->default('dicuci');
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->decimal('discount_kg', 10, 2)->default(0.00)->comment('Diskon dari promo');
            $table->timestamps();

            $table->index('santri_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
