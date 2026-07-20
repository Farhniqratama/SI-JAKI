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
        Schema::create('pt_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perguruan_tinggi_id')
                  ->constrained('perguruan_tinggi')
                  ->onDelete('cascade');
            $table->enum('address_type', ['utama', 'perluasan', 'psdku', 'pbjj']);
            $table->string('address', 255);
            $table->timestamps();

            $table->index(['perguruan_tinggi_id', 'address_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pt_addresses');
    }
};
