<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .footer {
            background: #6c757d;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SowarTech</h1>
        <p>Prueba de Configuración de Email</p>
    </div>
    
    <div class="content">
        <h2>¡Configuración Exitosa!</h2>
        
        <div class="success">
            <h3>✅ Email funcionando correctamente</h3>
            <p>Si puedes ver este email, significa que la configuración de SMTP está funcionando correctamente.</p>
        </div>
        
        <p>Este es un email de prueba para verificar que el sistema de facturación puede enviar emails correctamente.</p>
        
        <h3>Información de la prueba:</h3>
        <ul>
            <li><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</li>
            <li><strong>Servidor:</strong> {{ config('app.name') }}</li>
            <li><strong>Configuración:</strong> {{ config('mail.default') }}</li>
        </ul>
    </div>
    
    <div class="footer">
        <p><strong>SowarTech</strong></p>
        <p>Sistema de Facturación Electrónica</p>
        <p style="font-size: 12px; margin-top: 10px;">
            Este es un email de prueba automático.
        </p>
    </div>
</body>
</html> 