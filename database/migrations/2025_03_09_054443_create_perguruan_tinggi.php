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
        Schema::create('perguruan_tinggi', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('kode_pt');
            $table->string('nama_pt');
            $table->enum('jenis_pt', ['PTN', 'PTS'])->index();
            $table->enum('status_pt', ['Aktif', 'Tutup', 'Merger', 'Berubah Bentuk', 'Berubah Nama', 'Pindah Lokasi', 'Penegerian', 'Pembinaan', 'Tidak Terdata'])
                ->default('Aktif')
                ->index();
            $table->text('keterangan')->nullable();
            $table->date('tanggal')->nullable()->index();
            $table->string('file_sk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perguruan_tinggi');
    }
};
