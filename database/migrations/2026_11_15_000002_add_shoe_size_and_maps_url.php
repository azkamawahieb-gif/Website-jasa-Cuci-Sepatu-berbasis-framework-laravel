<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('shoe_size', 10)->nullable()->after('shoe_color');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('maps_url', 500)->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('shoe_size');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('maps_url');
        });
    }
};