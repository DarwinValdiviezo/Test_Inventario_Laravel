<?php
echo "<h1>Test de PHP en entorno web</h1>";

echo "<h2>Información de PHP:</h2>";
echo "<p><strong>Versión PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Archivo php.ini:</strong> " . php_ini_loaded_file() . "</p>";

echo "<h2>Extensiones cargadas:</h2>";
$extensions = get_loaded_extensions();
echo "<ul>";
foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "<li>$status $ext</li>";
}
echo "</ul>";

echo "<h2>Test de GD:</h2>";
if (extension_loaded('gd')) {
    echo "<p>✅ GD está cargada</p>";
    $gd_info = gd_info();
    echo "<p><strong>Versión GD:</strong> " . $gd_info['GD Version'] . "</p>";
    
    // Test básico de GD
    try {
        $im = imagecreate(100, 100);
        if ($im) {
            echo "<p>✅ Creación de imagen exitosa</p>";
            imagedestroy($im);
        } else {
            echo "<p>❌ Error al crear imagen</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ GD NO está cargada</p>";
}

echo "<h2>Test de QR Code:</h2>";
try {
    require_once '../vendor/autoload.php';
    
    $qr = new \Endroid\QrCode\QrCode('test');
    echo "<p>✅ QrCode creado</p>";
    
    $writer = new \Endroid\QrCode\Writer\PngWriter();
    echo "<p>✅ PngWriter creado</p>";
    
    $result = $writer->write($qr);
    echo "<p>✅ QR generado</p>";
    
    $dataUri = $result->getDataUri();
    $base64 = explode(',', $dataUri, 2)[1] ?? null;
    
    if ($base64) {
        echo "<p>✅ Base64 obtenido (longitud: " . strlen($base64) . ")</p>";
        echo "<img src='$dataUri' alt='QR Test' style='border: 1px solid #ccc;'>";
    } else {
        echo "<p>❌ No se pudo obtener base64</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error en QR: " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} 