<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            $table->string('nama_pemimpin_pt', 30)->nullable()->after('status_kelembagaan_pt');
            $table->string('nomor_kontak_pemimpin', 25)->nullable()->after('nama_pemimpin_pt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            $table->dropColumn(['nama_pemimpin_pt', 'nomor_kontak_pemimpin']);
        });
    }
};
