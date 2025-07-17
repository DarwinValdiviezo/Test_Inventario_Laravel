<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\FacturaSRIService;
use App\Models\FacturaDetalle;

/**
 * @property \App\Models\Cliente $cliente
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\FacturaDetalle[] $detalles
 * @property \App\Models\User $usuario
 */
class Factura extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cliente_id', 
        'usuario_id',
        'factura_original_id',
        'subtotal', 
        'iva', 
        'total', 
        'estado', 
        'motivo_anulacion',
        'created_by', 
        'updated_by',
        // Campos SRI
        'numero_secuencial',
        'cua',
        'firma_digital',
        'mensaje_autorizacion',
        'fecha_emision',
        'hora_emision',
        'ambiente',
        'tipo_emision',
        'tipo_documento',
        'forma_pago',
        'contenido_qr',
        'imagen_qr',
        // Nuevos campos para estado de firma y emisión
        'estado_firma',
        'fecha_firma',
        'estado_emision',
        'fecha_emision_email'
    ];

    protected $dates = ['deleted_at', 'fecha_emision'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_emision' => 'date',
        'fecha_firma' => 'datetime',
        'fecha_emision_email' => 'datetime'
    ];

    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function facturaOriginal()
    {
        return $this->belongsTo(Factura::class, 'factura_original_id');
    }

    public function facturasModificadas()
    {
        return $this->hasMany(Factura::class, 'factura_original_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relación con los detalles
     */
    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Verificar si la factura está anulada
     */
    public function isAnulada()
    {
        return $this->estado === 'anulada';
    }

    /**
     * Verificar si la factura está activa
     */
    public function isActiva()
    {
        return $this->estado === 'activa';
    }

    /**
     * Generar datos SRI para la factura
     */
    public function generarDatosSRI()
    {
        $service = new FacturaSRIService();
        $datosSRI = $service->prepararDatosSRI($this->subtotal);
        
        $this->fill($datosSRI);
        $this->save();
        
        return $this;
    }

    /**
     * Generar firma digital y QR para la factura
     */
    public function generarFirmaYQR()
    {
        $service = new FacturaSRIService();
        $datosFirmaQR = $service->generarFirmaYQR($this);
        $this->fill($datosFirmaQR);
        $this->save();
        return $this;
    }

    /**
     * Generar contenido QR para la factura
     */
    public function generarQR()
    {
        $service = new FacturaSRIService();
        $this->contenido_qr = $service->generarQRParaFactura($this);
        $this->save();
        
        return $this->contenido_qr;
    }

    /**
     * Verificar integridad de la firma digital
     */
    public function verificarFirmaDigital()
    {
        $service = new FacturaSRIService();
        return $service->verificarFirmaDigital($this, $this->firma_digital);
    }

    /**
     * Obtener datos del emisor
     */
    public function getDatosEmisor()
    {
        $service = new FacturaSRIService();
        return $service->getDatosEmisor();
    }

    /**
     * Formatear número secuencial para mostrar
     */
    public function getNumeroFormateado()
    {
        return $this->numero_secuencial ?? 'Pendiente';
    }

    /**
     * Formatear CUA para mostrar
     */
    public function getCUAFormateado()
    {
        return $this->cua ?? 'Pendiente';
    }

    /**
     * Verificar si la factura tiene datos SRI completos
     */
    public function tieneDatosSRI()
    {
        return !empty($this->numero_secuencial) && 
               !empty($this->cua) && 
               !empty($this->firma_digital);
    }

    /**
     * Obtener el estado de autorización SRI
     */
    public function getEstadoAutorizacion()
    {
        return $this->mensaje_autorizacion ?? 'PENDIENTE';
    }

    /**
     * Verificar si la factura está firmada
     */
    public function isFirmada()
    {
        return $this->estado_firma === 'FIRMADA';
    }

    /**
     * Verificar si la factura está emitida
     */
    public function isEmitida()
    {
        return $this->estado_emision === 'EMITIDA';
    }

    /**
     * Verificar si la factura está pendiente de firma
     */
    public function isPendienteFirma()
    {
        return $this->estado_firma === 'PENDIENTE' || empty($this->estado_firma);
    }

    /**
     * Verificar si la factura está pendiente de emisión
     */
    public function isPendienteEmision()
    {
        return $this->estado_emision === 'PENDIENTE' || empty($this->estado_emision);
    }

    /**
     * Firmar la factura digitalmente
     */
    public function firmarDigitalmente()
    {
        $service = new FacturaSRIService();
        $datosFirmaQR = $service->generarFirmaYQR($this);
        
        $this->fill($datosFirmaQR);
        $this->estado_firma = 'FIRMADA';
        $this->fecha_firma = now();
        $this->save();
        
        return $this;
    }

    /**
     * Emitir la factura (enviar por email)
     */
    public function emitir()
    {
        $this->estado_emision = 'EMITIDA';
        $this->fecha_emision_email = now();
        $this->save();
        
        // Enviar email al cliente
        try {
            $emailService = new \App\Services\EmailService();
            $clienteEmail = $this->cliente->email ?? null;
            
            if ($clienteEmail) {
                $asunto = 'Factura #' . $this->getNumeroFormateado() . ' - SowarTech';
                $mensaje = "Adjunto la factura #{$this->getNumeroFormateado()} por un total de $" . number_format((float)$this->total, 2) . ".\n\nGracias por su compra.\n\nSaludos cordiales,\nEquipo de SowarTech";
                
                $enviado = $emailService->enviarFactura($this, $clienteEmail, $asunto, $mensaje);
                
                if (!$enviado) {
                    \Log::warning("No se pudo enviar email para factura #{$this->id} a {$clienteEmail}");
                }
            } else {
                \Log::warning("Cliente de factura #{$this->id} no tiene email configurado");
            }
        } catch (\Exception $e) {
            \Log::error("Error enviando email para factura #{$this->id}: " . $e->getMessage());
        }
        
        return $this;
    }

    /**
     * Obtener el estado visual de la factura
     */
    public function getEstadoVisual()
    {
        if ($this->isAnulada()) {
            return ['texto' => 'ANULADA', 'clase' => 'danger', 'icono' => 'fas fa-ban'];
        }
        
        if ($this->isEmitida()) {
            return ['texto' => 'EMITIDA', 'clase' => 'success', 'icono' => 'fas fa-check-circle'];
        }
        
        if ($this->isFirmada()) {
            return ['texto' => 'FIRMADA', 'clase' => 'warning', 'icono' => 'fas fa-signature'];
        }
        
        return ['texto' => 'PENDIENTE', 'clase' => 'info', 'icono' => 'fas fa-clock'];
    }

    /**
     * Scope para facturas con datos SRI
     */
    public function scopeConDatosSRI($query)
    {
        return $query->whereNotNull('numero_secuencial')
                    ->whereNotNull('cua')
                    ->whereNotNull('firma_digital');
    }

    /**
     * Scope para facturas autorizadas
     */
    public function scopeAutorizadas($query)
    {
        return $query->where('mensaje_autorizacion', 'AUTORIZADO');
    }
}
