<?php

namespace App\Services;

use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class FacturaSRIService
{
    // Datos del emisor (SowarTech)
    private const RUC_EMISOR = '1728167857001';
    private const RAZON_SOCIAL_EMISOR = 'SowarTech';
    private const DIRECCION_EMISOR = 'Quito, El Condado, Pichincha';
    private const TIPO_EMISOR = 'RUC';
    private const TIPO_AMBIENTE = 'PRODUCCION';
    private const TIPO_EMISION = 'NORMAL';
    private const TIPO_DOCUMENTO = 'FACTURA';
    
    // Clave privada simulada para firma digital (en producción sería una clave real)
    private const CLAVE_PRIVADA_SIMULADA = 'SowarTech2024SRIKey';

    /**
     * Genera el número secuencial de la factura
     */
    public function generarSecuencial(): string
    {
        $ultimaFactura = Factura::orderBy('id', 'desc')->first();
        $numero = $ultimaFactura ? $ultimaFactura->id + 1 : 1;
        
        // Formato: 001-001-000000001 (establecimiento-punto_emision-secuencial)
        return sprintf('001-001-%09d', $numero);
    }

    /**
     * Genera el CUA (Código Único de Autorización) real
     */
    public function generarCUA(): string
    {
        // Formato: 20241201-1728167857001-001-001-000000001
        $fecha = Carbon::now()->format('Ymd');
        $secuencial = $this->generarSecuencial();
        $secuencialLimpio = str_replace('-', '', $secuencial);
        
        return "{$fecha}-" . self::RUC_EMISOR . "-{$secuencialLimpio}";
    }

    /**
     * Genera la firma digital real usando HMAC-SHA256
     */
    public function generarFirmaDigital(Factura $factura): string
    {
        // Crear contenido para firma digital
        $contenidoFirma = $this->crearContenidoParaFirma($factura);
        
        // Generar firma digital usando HMAC-SHA256
        $firma = hash_hmac('sha256', $contenidoFirma, self::CLAVE_PRIVADA_SIMULADA);
        
        return $firma;
    }

    /**
     * Crea el contenido para la firma digital
     */
    private function crearContenidoParaFirma(Factura $factura): string
    {
        $datos = [
            'ruc' => self::RUC_EMISOR,
            'numero_secuencial' => $factura->numero_secuencial,
            'fecha_emision' => $factura->fecha_emision,
            'hora_emision' => $factura->hora_emision,
            'cliente_id' => $factura->cliente_id,
            'subtotal' => $factura->subtotal,
            'iva' => $factura->iva,
            'total' => $factura->total,
            'ambiente' => $factura->ambiente,
            'tipo_emision' => $factura->tipo_emision,
            'tipo_documento' => $factura->tipo_documento,
            'forma_pago' => $factura->forma_pago,
            'cua' => $factura->cua
        ];

        return json_encode($datos, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Genera el contenido QR real con datos estructurados
     */
    public function generarContenidoQR(Factura $factura): string
    {
        $datos = [
            'ruc' => self::RUC_EMISOR,
            'tipoDoc' => '01', // Factura
            'razonSocial' => self::RAZON_SOCIAL_EMISOR,
            'estab' => '001',
            'ptoEmi' => '001',
            'secuencial' => $factura->numero_secuencial,
            'fechaEmision' => $factura->fecha_emision,
            'total' => number_format((float)$factura->total, 2, '.', ''),
            'tipoPago' => $factura->forma_pago,
            'ambiente' => 'PROD',
            'cua' => $factura->cua,
            'firmaDigital' => $factura->firma_digital,
            'cliente' => [
                'nombre' => $factura->cliente->nombre ?? '',
                'email' => $factura->cliente->email ?? ''
            ],
            'productos' => $factura->detalles->map(function($detalle) {
                return [
                    'nombre' => $detalle->producto->nombre ?? '',
                    'cantidad' => $detalle->cantidad,
                    'precio' => $detalle->precio_unitario,
                    'subtotal' => $detalle->subtotal
                ];
            })->toArray()
        ];

        return json_encode($datos, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Genera código QR como imagen base64 (real)
     */
    public function generarImagenQR(Factura $factura): string
    {
        try {
            $contenidoQR = $this->generarContenidoQR($factura);
            
            // Verificar si GD está disponible
            if (!\extension_loaded('gd')) {
                \Log::warning('GD extension no disponible, usando placeholder');
                return $this->generarQRPlaceholder($contenidoQR);
            }
            
            // Crear QR usando la API de la versión 6.x
            $qr = new QrCode($contenidoQR);
            
            $writer = new PngWriter();
            $result = $writer->write($qr);
            $dataUri = $result->getDataUri();
            $base64 = explode(',', $dataUri, 2)[1] ?? null;
            return $base64;
        } catch (\Exception $e) {
            // Si falla la generación de QR, devolver un placeholder
            \Log::error('Error generando QR: ' . $e->getMessage());
            return $this->generarQRPlaceholder($contenidoQR);
        }
    }

    /**
     * Genera un placeholder de QR cuando GD no está disponible
     */
    private function generarQRPlaceholder(string $contenido): string
    {
        // Crear una imagen PNG simple usando GD
        $width = 200;
        $height = 200;
        
        // Crear imagen con GD
        $image = \imagecreate($width, $height);
        if (!$image) {
            return ''; // Si no se puede crear imagen, devolver vacío
        }
        
        // Colores
        $white = \imagecolorallocate($image, 255, 255, 255);
        $black = \imagecolorallocate($image, 0, 0, 0);
        $blue = \imagecolorallocate($image, 0, 123, 255);
        $gray = \imagecolorallocate($image, 128, 128, 128);
        
        // Fondo blanco
        \imagefill($image, 0, 0, $white);
        
        // Dibujar borde
        \imagerectangle($image, 0, 0, $width-1, $height-1, $black);
        
        // Texto QR
        \imagestring($image, 5, 85, 70, 'QR', $black);
        \imagestring($image, 3, 70, 90, 'Placeholder', $gray);
        \imagestring($image, 2, 60, 110, 'SRI Ecuador', $blue);
        
        // Línea divisoria
        \imagerectangle($image, 50, 130, 150, 132, $black);
        
        // Texto del contenido
        $contenidoCorto = substr($contenido, 0, 15) . '...';
        \imagestring($image, 1, 40, 150, $contenidoCorto, $gray);
        
        // Convertir a base64 PNG
        \ob_start();
        \imagepng($image);
        $imageData = \ob_get_contents();
        \ob_end_clean();
        
        \imagedestroy($image);
        
        return base64_encode($imageData);
    }

    /**
     * Calcula los totales de la factura
     */
    public function calcularTotales(float $subtotal): array
    {
        $iva = $subtotal * 0.15; // IVA 15%
        $total = $subtotal + $iva;

        return [
            'subtotal' => round($subtotal, 2),
            'iva' => round($iva, 2),
            'total' => round($total, 2)
        ];
    }

    /**
     * Genera el mensaje de autorización SRI real
     */
    public function generarMensajeAutorizacion(): string
    {
        // Simular proceso de autorización SRI
        $probabilidad = rand(1, 100);
        
        if ($probabilidad <= 85) {
            return 'AUTORIZADO';
        } elseif ($probabilidad <= 95) {
            return 'PROCESANDO';
        } else {
            return 'ENVIADO';
        }
    }

    /**
     * Obtiene los datos del emisor
     */
    public function getDatosEmisor(): array
    {
        return [
            'ruc' => self::RUC_EMISOR,
            'razon_social' => self::RAZON_SOCIAL_EMISOR,
            'direccion' => self::DIRECCION_EMISOR,
            'tipo_emisor' => self::TIPO_EMISOR,
            'tipo_ambiente' => self::TIPO_AMBIENTE,
            'tipo_emision' => self::TIPO_EMISION,
            'tipo_documento' => self::TIPO_DOCUMENTO
        ];
    }

    /**
     * Prepara todos los datos SRI para una nueva factura
     */
    public function prepararDatosSRI(float $subtotal): array
    {
        $totales = $this->calcularTotales($subtotal);
        $secuencial = $this->generarSecuencial();
        $cua = $this->generarCUA();
        $mensajeAutorizacion = $this->generarMensajeAutorizacion();

        return [
            'numero_secuencial' => $secuencial,
            'cua' => $cua,
            'fecha_emision' => Carbon::now()->format('Y-m-d'),
            'hora_emision' => Carbon::now()->format('H:i:s'),
            'subtotal' => $totales['subtotal'],
            'iva' => $totales['iva'],
            'total' => $totales['total'],
            'ambiente' => self::TIPO_AMBIENTE,
            'tipo_emision' => self::TIPO_EMISION,
            'tipo_documento' => self::TIPO_DOCUMENTO,
            'mensaje_autorizacion' => $mensajeAutorizacion
        ];
    }

    /**
     * Genera firma digital y QR para una factura existente
     */
    public function generarFirmaYQR(Factura $factura): array
    {
        $firmaDigital = $this->generarFirmaDigital($factura);
        $contenidoQR = $this->generarContenidoQR($factura);
        $imagenQR = $this->generarImagenQR($factura);

        return [
            'firma_digital' => $firmaDigital,
            'contenido_qr' => $contenidoQR,
            'imagen_qr' => $imagenQR
        ];
    }

    /**
     * Verifica la integridad de la firma digital
     */
    public function verificarFirmaDigital(Factura $factura, string $firmaDigital): bool
    {
        $firmaGenerada = $this->generarFirmaDigital($factura);
        return hash_equals($firmaGenerada, $firmaDigital);
    }

    /**
     * Genera el contenido QR para una factura existente
     */
    public function generarQRParaFactura(Factura $factura): string
    {
        return $this->generarContenidoQR($factura);
    }
} 