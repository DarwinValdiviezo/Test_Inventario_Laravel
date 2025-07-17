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
            // Solo agregar campos que no existen
            if (!Schema::hasColumn('facturas', 'numero_secuencial')) {
                $table->string('numero_secuencial')->nullable()->after('total');
            }
            if (!Schema::hasColumn('facturas', 'fecha_emision')) {
                $table->date('fecha_emision')->nullable()->after('cua');
            }
            if (!Schema::hasColumn('facturas', 'hora_emision')) {
                $table->time('hora_emision')->nullable()->after('fecha_emision');
            }
            if (!Schema::hasColumn('facturas', 'ambiente')) {
                $table->string('ambiente')->default('PRODUCCION')->after('hora_emision');
            }
            if (!Schema::hasColumn('facturas', 'tipo_emision')) {
                $table->string('tipo_emision')->default('NORMAL')->after('ambiente');
            }
            if (!Schema::hasColumn('facturas', 'tipo_documento')) {
                $table->string('tipo_documento')->default('FACTURA')->after('tipo_emision');
            }
            if (!Schema::hasColumn('facturas', 'mensaje_autorizacion')) {
                $table->string('mensaje_autorizacion')->nullable()->after('tipo_documento');
            }
            if (!Schema::hasColumn('facturas', 'contenido_qr')) {
                $table->text('contenido_qr')->nullable()->after('firma_digital');
            }
            if (!Schema::hasColumn('facturas', 'imagen_qr')) {
                $table->text('imagen_qr')->nullable()->after('contenido_qr');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn([
                'numero_secuencial',
                'fecha_emision',
                'hora_emision',
                'ambiente',
                'tipo_emision',
                'tipo_documento',
                'mensaje_autorizacion',
                'contenido_qr',
                'imagen_qr'
            ]);
        });
    }
};
