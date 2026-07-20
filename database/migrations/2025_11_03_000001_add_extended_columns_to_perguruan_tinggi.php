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
            if (!Schema::hasColumn('perguruan_tinggi', 'nama_pt_sk')) {
                $table->string('nama_pt_sk')->nullable()->after('nama_pt');
            }

            if (!Schema::hasColumn('perguruan_tinggi', 'status_kelembagaan_pt')) {
                $table->string('status_kelembagaan_pt')->nullable()->after('status_pt');
            }

            if (!Schema::hasColumn('perguruan_tinggi', 'alamat_kampus_utama')) {
                $table->string('alamat_kampus_utama', 150)->nullable()->after('status_kelembagaan_pt');
            }

            if (!Schema::hasColumn('perguruan_tinggi', 'alamat_kampus_perluasan')) {
                $table->string('alamat_kampus_perluasan', 150)->nullable()->after('alamat_kampus_utama');
            }

            if (!Schema::hasColumn('perguruan_tinggi', 'alamat_kampus_psdku')) {
                $table->string('alamat_kampus_psdku', 150)->nullable()->after('alamat_kampus_perluasan');
            }

            if (!Schema::hasColumn('perguruan_tinggi', 'alamat_kampus_pbjj')) {
                $table->string('alamat_kampus_pbjj', 150)->nullable()->after('alamat_kampus_psdku');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perguruan_tinggi', function (Blueprint $table) {
            $columns = [
                'alamat_kampus_pbjj',
                'alamat_kampus_psdku',
                'alamat_kampus_perluasan',
                'alamat_kampus_utama',
                'status_kelembagaan_pt',
                'nama_pt_sk',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('perguruan_tinggi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
