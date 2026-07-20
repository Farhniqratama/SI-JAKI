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
        Schema::table('maintenance_modes', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_modes', 'type')) {
                $table->enum('type', ['maintenance', 'construction'])
                    ->default('maintenance')
                    ->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_modes', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_modes', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
