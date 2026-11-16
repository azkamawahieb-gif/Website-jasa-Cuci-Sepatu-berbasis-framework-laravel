<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained();
            $table->string('shoe_name');
            $table->string('shoe_color')->nullable();
            $table->text('condition_notes')->nullable();
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->unsignedInteger('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};