<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

try {
    $qr = new QrCode('test');
    echo "Métodos disponibles:\n";
    $methods = get_class_methods($qr);
    foreach ($methods as $method) {
        echo "- $method\n";
    }
    
    echo "\nProbando generación básica:\n";
    $writer = new PngWriter();
    $result = $writer->write($qr);
    echo "QR generado exitosamente\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 