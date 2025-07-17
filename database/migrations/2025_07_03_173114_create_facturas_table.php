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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('factura_original_id')->nullable();
            // SRI y datos electrónicos
            $table->string('ruc_emisor', 20)->default('1728167857001');
            $table->string('razon_social_emisor', 100)->default('SowarTech');
            $table->string('direccion_emisor', 150)->default('Quito, El Condado, Pichincha');
            $table->string('num_autorizacion_sri', 49)->nullable();
            $table->string('secuencial', 20)->nullable();
            $table->string('establecimiento', 3)->default('001');
            $table->string('punto_emision', 3)->default('001');
            $table->string('numero_factura', 17)->nullable(); // 001-001-000000001
            $table->string('cua', 49)->nullable();
            $table->string('firma_digital', 255)->nullable();
            $table->string('codigo_qr', 255)->nullable();
            $table->string('forma_pago', 50)->nullable();
            $table->timestamp('fecha_autorizacion')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['activa', 'anulada'])->default('activa');
            $table->text('motivo_anulacion')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Claves foráneas
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('factura_original_id')->references('id')->on('facturas')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
