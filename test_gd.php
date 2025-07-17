<?php
require 'vendor/autoload.php';

echo "=== Test de GD Extension ===\n";

// Verificar si GD está cargada
if (extension_loaded('gd')) {
    echo "✅ GD extension está cargada\n";
    echo "Versión GD: " . gd_info()['GD Version'] . "\n";
} else {
    echo "❌ GD extension NO está cargada\n";
    exit(1);
}

// Verificar funciones de GD
if (function_exists('imagecreate')) {
    echo "✅ imagecreate() disponible\n";
} else {
    echo "❌ imagecreate() NO disponible\n";
}

if (function_exists('imagepng')) {
    echo "✅ imagepng() disponible\n";
} else {
    echo "❌ imagepng() NO disponible\n";
}

// Test básico de creación de imagen
try {
    $im = imagecreate(100, 100);
    if ($im) {
        echo "✅ Creación de imagen exitosa\n";
        imagedestroy($im);
    } else {
        echo "❌ Error al crear imagen\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test de QR Code ===\n";

try {
    $qr = new \Endroid\QrCode\QrCode('test');
    echo "✅ QrCode creado exitosamente\n";
    
    $writer = new \Endroid\QrCode\Writer\PngWriter();
    echo "✅ PngWriter creado exitosamente\n";
    
    $result = $writer->write($qr);
    echo "✅ QR generado exitosamente\n";
    
    $dataUri = $result->getDataUri();
    echo "✅ Data URI obtenido: " . substr($dataUri, 0, 50) . "...\n";
    
} catch (Exception $e) {
    echo "❌ Error en QR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 