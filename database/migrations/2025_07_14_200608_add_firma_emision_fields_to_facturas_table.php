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
        Schema::table('facturas', function (Blueprint $table) {
            $table->enum('estado_firma', ['PENDIENTE', 'FIRMADA'])->default('PENDIENTE')->after('estado');
            $table->timestamp('fecha_firma')->nullable()->after('estado_firma');
            $table->enum('estado_emision', ['PENDIENTE', 'EMITIDA'])->default('PENDIENTE')->after('fecha_firma');
            $table->timestamp('fecha_emision_email')->nullable()->after('estado_emision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['estado_firma', 'fecha_firma', 'estado_emision', 'fecha_emision_email']);
        });
    }
};
