<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10);                                  // jemput | antar
            $table->text('address');
            $table->foreignId('delivery_zone_id')->constrained();
            $table->unsignedInteger('fee')->default(0);
            $table->dateTime('scheduled_at')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('dijadwalkan');       // dijadwalkan | dalam_perjalanan | selesai | gagal
            $table->dateTime('completed_at')->nullable();
            $table->string('proof_photo')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};