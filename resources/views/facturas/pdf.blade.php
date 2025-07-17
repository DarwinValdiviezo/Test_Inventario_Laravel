<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $factura->getNumeroFormateado() }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0 0 20px 0;
            color: #232c47;
            font-size: 12px;
            background: #f8f9fa;
        }
        .pdf-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.07);
            padding: 30px 30px 20px 30px;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 18px;
            margin-bottom: 18px;
        }
        .logo {
            width: 70px;
            height: 70px;
            margin-right: 18px;
        }
        .company-info {
            flex: 1;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 2px;
        }
        .company-details {
            font-size: 12px;
            color: #666;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: bold;
            color: #232c47;
            margin: 18px 0 10px 0;
            text-align: center;
            letter-spacing: 1px;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: bold;
            color: #fff;
            background: #007bff;
            margin-left: 8px;
        }
        .badge-success { background: #28a745; }
        .badge-warning { background: #ffc107; color: #232c47; }
        .badge-danger { background: #dc3545; }
        .badge-info { background: #17a2b8; }
        .badge-secondary { background: #6c757d; }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        .col {
            flex: 1;
            min-width: 220px;
            padding: 0 10px;
        }
        .card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px 18px;
            margin-bottom: 18px;
            border: 1px solid #e3e6f0;
        }
        .card-title {
            font-size: 15px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }
        .info-row {
            margin-bottom: 5px;
            font-size: 12px;
        }
        .info-label {
            font-weight: bold;
            color: #232c47;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 0 0;
        }
        .table th {
            background: #007bff;
            color: #fff;
            padding: 10px 6px;
            font-size: 12px;
            text-align: left;
        }
        .table td {
            padding: 8px 6px;
            border-bottom: 1px solid #e3e6f0;
            font-size: 12px;
        }
        .table tr:nth-child(even) { background: #f4f8fb; }
        .product-img {
            width: 36px;
            height: 36px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 8px;
            vertical-align: middle;
        }
        .product-name {
            font-weight: bold;
            color: #232c47;
        }
        .product-desc {
            color: #666;
            font-size: 10px;
        }
        .quantity-badge {
            display: inline-block;
            background: #007bff;
            color: #fff;
            border-radius: 8px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: bold;
        }
        .price {
            text-align: right;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-top: 18px;
        }
        .total-row {
            margin-bottom: 5px;
            font-size: 13px;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }
        .qr-section {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            margin: 30px 0 10px 0;
        }
        .qr-block {
            text-align: center;
            flex: 1;
        }
        .qr-image {
            width: 120px;
            height: 120px;
            margin-bottom: 5px;
        }
        .firma-block {
            flex: 2;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
            padding: 10px 18px;
        }
        .firma-title {
            color: #007bff;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .firma-status {
            display: inline-block;
            background: #28a745;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            border-radius: 5px;
            padding: 2px 10px;
            margin-bottom: 5px;
        }
        .firma-status-pendiente {
            background: #ffc107;
            color: #232c47;
        }
        .firma-desc {
            color: #28a745;
            font-size: 10px;
            margin-bottom: 4px;
        }
        .firma-datos {
            font-size: 9px;
            word-break: break-all;
            background: #f8f9fa;
            padding: 6px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
            margin-top: 7px;
        }
        .qr-content {
            font-size: 9px;
            color: #b30059;
            background: #f8f9fa;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 6px;
            margin-top: 8px;
            word-break: break-all;
        }
        .legal {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0 0 0;
            font-size: 10px;
            color: #856404;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #e3e6f0;
            padding-top: 12px;
        }
    </style>
</head>
<body>
<div class="pdf-container">
    <div class="header">
        <img src="{{ public_path('img/logo.png') }}" class="logo" alt="Logo SowarTech">
        <div class="company-info">
            <div class="company-name">SowarTech</div>
            <div class="company-details">
                Quito, El Condado, Pichincha<br>
                RUC: 1728167857001<br>
                Email: info@sowartech.com
            </div>
        </div>
    </div>
    <div class="invoice-title">
        FACTURA ELECTRÓNICA #{{ $factura->getNumeroFormateado() }}
        <span class="badge badge-{{ $factura->estado === 'activa' ? 'success' : ($factura->estado === 'anulada' ? 'danger' : 'secondary') }}">{{ strtoupper($factura->estado) }}</span>
    </div>
    <div class="row">
        <div class="col card">
            <div class="card-title">Cliente</div>
            <div class="info-row"><span class="info-label">Nombre:</span> {{ $factura->cliente->nombre ?? 'Cliente eliminado' }}</div>
            @if($factura->cliente)
            <div class="info-row"><span class="info-label">Email:</span> {{ $factura->cliente->email ?? 'No especificado' }}</div>
            <div class="info-row"><span class="info-label">Teléfono:</span> {{ $factura->cliente->telefono ?? 'No especificado' }}</div>
            @endif
        </div>
        <div class="col card">
            <div class="card-title">Factura</div>
            <div class="info-row"><span class="info-label">Número:</span> #{{ $factura->getNumeroFormateado() }}</div>
            <div class="info-row"><span class="info-label">Fecha:</span> {{ $factura->created_at->format('d/m/Y') }}</div>
            <div class="info-row"><span class="info-label">Hora:</span> {{ $factura->created_at->format('H:i') }}</div>
            <div class="info-row"><span class="info-label">Vendedor:</span> {{ $factura->usuario->name ?? 'Usuario eliminado' }}</div>
            <div class="info-row"><span class="info-label">Forma de Pago:</span> {{ $factura->forma_pago ?? 'EFECTIVO' }}</div>
        </div>
        <div class="col card">
            <div class="card-title">Datos SRI</div>
            <div class="info-row"><span class="info-label">Secuencial:</span> {{ $factura->getNumeroFormateado() }}</div>
            <div class="info-row"><span class="info-label">CUA:</span> {{ $factura->getCUAFormateado() }}</div>
            <div class="info-row"><span class="info-label">Ambiente:</span> {{ $factura->ambiente ?? 'PRODUCCION' }}</div>
            <div class="info-row"><span class="info-label">Estado SRI:</span> <span class="badge badge-{{ $factura->getEstadoAutorizacion() === 'AUTORIZADO' ? 'success' : ($factura->getEstadoAutorizacion() === 'PROCESANDO' ? 'warning' : 'info') }}">{{ $factura->getEstadoAutorizacion() }}</span></div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->detalles as $index => $detalle)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @if($detalle->producto && $detalle->producto->imagen)
                        <img src="{{ public_path('storage/productos/' . $detalle->producto->imagen) }}" class="product-img" alt="{{ $detalle->producto->nombre }}">
                    @endif
                    <span class="product-name">{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</span>
                    @if($detalle->producto && $detalle->producto->descripcion)
                        <div class="product-desc">{{ $detalle->producto->descripcion }}</div>
                    @endif
                </td>
                <td><span class="quantity-badge">{{ $detalle->cantidad }}</span></td>
                <td class="price">${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="price">${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total-section">
        <div class="total-row"><span class="info-label">Subtotal:</span> ${{ number_format($factura->subtotal, 2) }}</div>
        <div class="total-row"><span class="info-label">IVA ({{ number_format(($factura->iva / max($factura->subtotal,1))*100, 0) }}%):</span> ${{ number_format($factura->iva, 2) }}</div>
        <div class="total-row total-amount"><span class="info-label">TOTAL:</span> ${{ number_format($factura->total, 2) }}</div>
    </div>
    <div class="qr-section">
        <div class="qr-block">
            <div style="font-weight: bold; color: #007bff; font-size: 13px; margin-bottom: 6px;">Código QR SRI</div>
            @if($factura->qr_code)
                <img src="data:image/png;base64,{{ $factura->qr_code }}" class="qr-image" alt="QR Code">
                <div style="font-size: 10px; color: #666;">Escanee para verificar autenticidad</div>
            @else
                <div style="font-size: 10px; color: #dc3545;">QR no disponible</div>
            @endif
            @if($factura->contenido_qr)
            <div class="qr-content"><strong>Contenido QR:</strong> {{ $factura->contenido_qr }}</div>
            @endif
        </div>
        <div class="firma-block">
            <div class="firma-title">Firma Digital</div>
            @if($factura->isFirmada())
                <span class="firma-status">FIRMA VÁLIDA</span>
                <div class="firma-desc">La firma digital es válida</div>
            @else
                <span class="firma-status firma-status-pendiente">PENDIENTE</span>
                <div class="firma-desc">Requiere firma digital</div>
            @endif
            <div class="firma-datos"><strong>Firma:</strong><br>{{ $factura->firma_digital ?? 'No disponible' }}</div>
            <div class="firma-datos"><strong>Autorizado por:</strong> {{ $factura->usuario->name ?? 'Sistema' }}</div>
            <div class="firma-datos"><strong>Fecha de firma:</strong> {{ $factura->fecha_firma_digital ? $factura->fecha_firma_digital->format('d/m/Y H:i:s') : 'No firmada' }}</div>
        </div>
    </div>
    <div class="legal">
        <strong>AVISO:</strong> Esta factura electrónica ha sido generada por el Sistema de Rentas Internas del Ecuador. La firma digital y el código QR garantizan la autenticidad e integridad del documento. Cualquier modificación invalidará la factura.
    </div>
    <div class="footer">
        <p><strong>SowarTech</strong> - Sistema de Facturación Electrónica</p>
        <p>Esta factura cumple con los requisitos del SRI de Ecuador</p>
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</div>
</body>
</html> 