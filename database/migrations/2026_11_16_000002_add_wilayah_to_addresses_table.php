<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('province', 100)->nullable()->after('address');
            $table->string('regency', 100)->nullable()->after('province');
            $table->string('district', 100)->nullable()->after('regency');
            $table->string('detail', 200)->nullable()->after('district');
        });

        // Alamat lama: kota disalin ke kolom Kabupaten/Kota.
        DB::table('addresses')->whereNull('regency')->update(['regency' => DB::raw('city')]);
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['province', 'regency', 'district', 'detail']);
        });
    }
};