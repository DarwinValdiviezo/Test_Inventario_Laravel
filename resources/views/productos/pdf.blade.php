<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f8f9fa; }
        tr:nth-child(even) { background: #f4f4f4; }
        h2 { margin-bottom: 0; }
        .small { color: #888; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Reporte de Productos</h2>
    <div class="small">Generado: {{ now()->format('d/m/Y H:i') }}</div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->categoria->nombre ?? 'Sin categoría' }}</td>
                <td>{{ $p->stock }}</td>
                <td>${{ number_format($p->precio, 2) }}</td>
                <td>{{ $p->deleted_at ? 'Eliminado' : 'Activo' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 