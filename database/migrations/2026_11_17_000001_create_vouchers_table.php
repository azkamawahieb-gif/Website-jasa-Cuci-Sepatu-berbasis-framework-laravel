<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('description')->nullable();
            $table->string('type', 10)->default('nominal');            // persen | nominal
            $table->unsignedInteger('value');                            // persen (1-100) atau rupiah
            $table->unsignedInteger('min_order')->default(0);            // minimal subtotal layanan
            $table->unsignedInteger('max_discount')->nullable();         // batas potongan untuk tipe persen
            $table->unsignedInteger('quota')->nullable();                // null = tanpa batas
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_channel', 30)->nullable()->after('payment_method');   // BCA, Dana, GoPay, ...
            $table->string('voucher_code', 30)->nullable()->after('delivery_fee_total');
            $table->unsignedInteger('discount_total')->default(0)->after('voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_channel', 'voucher_code', 'discount_total']);
        });

        Schema::dropIfExists('vouchers');
    }
};