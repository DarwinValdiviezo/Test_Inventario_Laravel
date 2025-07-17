<?php
require 'vendor/autoload.php';

// Simular entorno web
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_HOST'] = 'localhost';

echo "=== Test QR en entorno web ===\n";

try {
    // Simular el servicio
    $contenidoQR = json_encode([
        'ruc' => '1728167857001',
        'tipoDoc' => '01',
        'razonSocial' => 'SowarTech',
        'estab' => '001',
        'ptoEmi' => '001',
        'secuencial' => '001-001-000000001',
        'fechaEmision' => '2024-12-01',
        'total' => '100.00',
        'tipoPago' => 'EFECTIVO',
        'ambiente' => 'PROD',
        'cua' => '20241201-1728167857001-001001000000001'
    ], JSON_UNESCAPED_UNICODE);

    echo "Contenido QR: " . substr($contenidoQR, 0, 100) . "...\n";
    
    $qr = new \Endroid\QrCode\QrCode($contenidoQR);
    echo "✅ QrCode creado\n";
    
    $writer = new \Endroid\QrCode\Writer\PngWriter();
    echo "✅ PngWriter creado\n";
    
    $result = $writer->write($qr);
    echo "✅ QR generado\n";
    
    $dataUri = $result->getDataUri();
    $base64 = explode(',', $dataUri, 2)[1] ?? null;
    
    if ($base64) {
        echo "✅ Base64 obtenido: " . substr($base64, 0, 50) . "...\n";
        echo "Longitud base64: " . strlen($base64) . "\n";
    } else {
        echo "❌ No se pudo obtener base64\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
} 