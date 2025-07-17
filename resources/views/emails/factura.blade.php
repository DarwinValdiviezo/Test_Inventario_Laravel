<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $factura->getNumeroFormateado() }}</title>
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
        .factura-info {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
        }
        .footer {
            background: #6c757d;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SowarTech</h1>
        <p>Sistema de Facturación Electrónica</p>
    </div>
    
    <div class="content">
        <h2>Factura Electrónica #{{ $factura->getNumeroFormateado() }}</h2>
        
        <div class="factura-info">
            <p><strong>Cliente:</strong> {{ $factura->cliente->nombre ?? 'Cliente eliminado' }}</p>
            <p><strong>Fecha:</strong> {{ $factura->created_at->format('d/m/Y') }}</p>
            <p><strong>Total:</strong> ${{ number_format($factura->total, 2) }}</p>
            <p><strong>Estado:</strong> 
                @if($factura->isEmitida())
                    <span style="color: #28a745;">✓ EMITIDA</span>
                @elseif($factura->isFirmada())
                    <span style="color: #ffc107;">✓ FIRMADA</span>
                @else
                    <span style="color: #6c757d;">PENDIENTE</span>
                @endif
            </p>
        </div>
        
        @if($mensaje)
            <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <h3>Mensaje:</h3>
                <p>{{ $mensaje }}</p>
            </div>
        @endif
        
        <div style="text-align: center; margin: 20px 0;">
            <p>La factura se encuentra adjunta a este email en formato PDF.</p>
            <p>Esta factura electrónica cumple con los requisitos del SRI de Ecuador.</p>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>SowarTech</strong></p>
        <p>Quito, El Condado, Pichincha</p>
        <p>RUC: 1728167857001</p>
        <p>Email: info@sowartech.com</p>
        <p style="font-size: 12px; margin-top: 10px;">
            Este es un email automático del sistema de facturación. 
            Por favor no responda a este mensaje.
        </p>
    </div>
</body>
</html> 