<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('user_id')
                ->constrained('orders')->nullOnDelete();
            $table->index(['order_id', 'id']);
        });

        // Pesan lama dikaitkan ke pesanan terbaru milik pengguna itu
        $penggunaIds = DB::table('chat_messages')->whereNull('order_id')->distinct()->pluck('user_id');

        foreach ($penggunaIds as $uid) {
            $orderId = DB::table('orders')->where('user_id', $uid)->max('id');

            if ($orderId) {
                DB::table('chat_messages')
                    ->where('user_id', $uid)->whereNull('order_id')
                    ->update(['order_id' => $orderId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
        });
    }
};