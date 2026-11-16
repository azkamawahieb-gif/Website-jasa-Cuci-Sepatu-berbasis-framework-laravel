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
            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained();
            $table->string('status', 30)->default('menunggu_konfirmasi')->index();
            $table->string('payment_status', 20)->default('belum_bayar');   // belum_bayar | lunas
            $table->string('payment_method', 20)->nullable();                // tunai | transfer | qris
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('delivery_fee_total')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->date('estimated_done_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};