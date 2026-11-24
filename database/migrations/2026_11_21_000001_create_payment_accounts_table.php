<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 30)->unique();
            $table->string('account_number', 40);
            $table->string('account_name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pindahkan rekening bawaan dari config/pembayaran.php
        $now = now();
        DB::table('payment_accounts')->insert([
            ['bank_name' => 'BCA',     'account_number' => '1234567890',    'account_name' => 'Cucci.in', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['bank_name' => 'BRI',     'account_number' => '098765432101',  'account_name' => 'Cucci.in', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['bank_name' => 'Mandiri', 'account_number' => '1230009876543', 'account_name' => 'Cucci.in', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['bank_name' => 'BNI',     'account_number' => '0123456789',    'account_name' => 'Cucci.in', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};