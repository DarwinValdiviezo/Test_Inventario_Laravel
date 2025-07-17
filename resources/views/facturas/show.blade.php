@extends('layouts.app')

@section('title', 'Detalle de Factura')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
</script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home"></i> Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('facturas.index') }}"><i class="bx bx-receipt"></i> Facturas</a></li>
          <li class="breadcrumb-item active" aria-current="page"><i class="bx bx-show"></i> Factura #{{ $factura->getNumeroFormateado() }}</li>
                </ol>
            </nav>
        </div>
    </div>
<!-- Barra de Estado de Firma y Emisión (PROGRESIVA y ÉPICA) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card card-outline card-info shadow-lg mb-4">
            <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-shield me-2"></i> Progreso de Factura Electrónica</h5>
            </div>
            <div class="card-body">
                <!-- Barra de progreso tipo steps -->
                <div class="d-flex justify-content-between align-items-center mb-3" style="gap: 0.5rem;">
                    <!-- Paso 1: Creada -->
                    <div class="text-center flex-fill">
                        <div class="rounded-circle mx-auto mb-1" style="width: 38px; height: 38px; background: #28a745; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="bx bx-file"></i>
                        </div>
                        <div style="font-size: 12px;">Creada</div>
                    </div>
                    <div class="flex-fill" style="height: 6px; background: {{ $factura->isFirmada() ? '#28a745' : '#dee2e6' }}; border-radius: 3px; transition: background 0.5s;"></div>
                    <!-- Paso 2: Firmada -->
                    <div class="text-center flex-fill">
                        <div class="rounded-circle mx-auto mb-1" style="width: 38px; height: 38px; background: {{ $factura->isFirmada() ? '#28a745' : '#dee2e6' }}; color: {{ $factura->isFirmada() ? '#fff' : '#6c757d' }}; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; transition: background 0.5s, color 0.5s;">
                            <i class="bx bx-shield"></i>
                        </div>
                        <div style="font-size: 12px;">Firmada</div>
                    </div>
                    <div class="flex-fill" style="height: 6px; background: {{ $factura->isEmitida() ? '#28a745' : '#dee2e6' }}; border-radius: 3px; transition: background 0.5s;"></div>
                    <!-- Paso 3: Emitida -->
                    <div class="text-center flex-fill">
                        <div class="rounded-circle mx-auto mb-1" style="width: 38px; height: 38px; background: {{ $factura->isEmitida() ? '#28a745' : '#dee2e6' }}; color: {{ $factura->isEmitida() ? '#fff' : '#6c757d' }}; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; transition: background 0.5s, color 0.5s;">
                            <i class="bx bx-envelope"></i>
                        </div>
                        <div style="font-size: 12px;">Emitida</div>
                    </div>
                    <div class="flex-fill" style="height: 6px; background: {{ $factura->isEmitida() && $factura->estado === 'activa' ? '#28a745' : '#dee2e6' }}; border-radius: 3px; transition: background 0.5s;"></div>
                    <!-- Paso 4: Enviada -->
                    <div class="text-center flex-fill">
                        <div class="rounded-circle mx-auto mb-1" style="width: 38px; height: 38px; background: {{ session('success') && $factura->isEmitida() ? '#28a745' : '#dee2e6' }}; color: {{ session('success') && $factura->isEmitida() ? '#fff' : '#6c757d' }}; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; transition: background 0.5s, color 0.5s;">
                            <i class="bx bx-send"></i>
                        </div>
                        <div style="font-size: 12px;">Enviada</div>
                    </div>
                </div>
                <!-- Badges de estado -->
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center mb-3">
                    <span class="badge bg-label-{{ $factura->isFirmada() ? 'success' : 'warning' }} fs-6">
                        <i class="bx bx-shield me-1"></i>
                        {{ $factura->isFirmada() ? 'Firmada' : 'Pendiente de Firma' }}
                    </span>
                    <span class="badge bg-label-{{ $factura->isEmitida() ? 'success' : 'info' }} fs-6">
                        <i class="bx bx-envelope me-1"></i>
                        {{ $factura->isEmitida() ? 'Emitida' : 'Pendiente de Emisión' }}
                    </span>
                    <span class="badge bg-label-{{ $factura->getEstadoAutorizacion() === 'AUTORIZADO' ? 'success' : ($factura->getEstadoAutorizacion() === 'PROCESANDO' ? 'warning' : 'info') }} fs-6">
                        <i class="bx bx-shield me-1"></i> Estado SRI: {{ $factura->getEstadoAutorizacion() }}
                    </span>
                </div>
                <!-- Botones de acción grandes y claros -->
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    @if(!$factura->isFirmada())
                        <form action="{{ route('facturas.firmar', $factura) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-lg shadow" style="font-size:1.3rem; min-width:220px;">🖊️ Firmar Factura</button>
                        </form>
                    @endif
                    @if($factura->isFirmada() && !$factura->isEmitida())
                        <form action="{{ route('facturas.emitir', $factura) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg shadow" style="font-size:1.3rem; min-width:220px;">📤 Emitir Factura</button>
                        </form>
                    @endif
                    @if($factura->isEmitida())
                        <button type="button" class="btn btn-primary btn-lg shadow" style="font-size:1.3rem; min-width:220px;" data-bs-toggle="modal" data-bs-target="#modalEnviarEmailFactura{{ $factura->id }}">✉️ Enviar por Correo</button>
                    @endif
                </div>
            </div>
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="bx bx-info-circle me-1"></i>
                    1. Primero <strong>firme</strong> la factura.<br>
                    2. Luego <strong>emítala</strong> electrónicamente.<br>
                    3. Finalmente, <strong>envíela por correo</strong> al cliente.<br>
                    El progreso se marcará en verde a medida que avance cada paso.
                </small>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0"><i class="bx bx-receipt me-2"></i> Factura Electrónica #{{ $factura->getNumeroFormateado() }}</h4>
          <span class="badge bg-label-{{ $factura->estado === 'activa' ? 'success' : ($factura->estado === 'anulada' ? 'danger' : 'secondary') }} fs-6">
            <i class="bx bx-check-circle me-1"></i> {{ ucfirst($factura->estado) }}
                        </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-muted"><i class="bx bx-user me-2 text-info"></i>Cliente</label>
                <div class="fw-semibold fs-5">{{ $factura->cliente->nombre ?? 'Cliente eliminado' }}</div>
                                @if($factura->cliente)
                  <small class="text-muted"><i class="bx bx-envelope me-1"></i>{{ $factura->cliente->email }}</small>
                                @endif
                            </div>
              <div class="mb-3">
                <label class="form-label text-muted"><i class="bx bx-user-pin me-2 text-info"></i>Vendedor</label>
                <div class="fs-5">{{ $factura->usuario->name ?? 'Usuario eliminado' }}</div>
                                @if($factura->usuario)
                  <small class="text-muted"><i class="bx bx-envelope me-1"></i>{{ $factura->usuario->email }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-muted"><i class="bx bx-calendar me-2 text-info"></i>Fecha de Emisión</label>
                <div class="fs-5">{{ $factura->fecha_emision ? $factura->fecha_emision->format('d/m/Y') : $factura->created_at->format('d/m/Y') }}</div>
                                @if($factura->hora_emision)
                  <small class="text-muted"><i class="bx bx-time me-1"></i>{{ $factura->hora_emision }}</small>
                                @endif
                            </div>
              <div class="mb-3">
                <label class="form-label text-muted"><i class="bx bx-dollar me-2 text-info"></i>Total</label>
                <div class="fs-4 fw-bold text-success">${{ number_format($factura->total, 2) }}</div>
                            </div>
              <div class="mb-3">
                <label class="form-label text-muted"><i class="bx bx-box me-2 text-info"></i>Productos</label>
                <div class="fs-5">{{ $factura->detalles->count() }} productos</div>
                            </div>
                        </div>
                    </div>
                </div>
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
          <small class="text-muted"><i class="bx bx-calendar me-1"></i> Factura creada: {{ $factura->created_at->diffForHumans() }}</small>
                        <div class="d-flex gap-2">
                            @if(!$factura->trashed())
                            @can('update', $factura)
                <a href="{{ route('facturas.edit', $factura->id) }}" class="btn btn-warning"><i class="bx bx-edit me-1"></i> Editar</a>
                            @endcan
              <a href="{{ route('facturas.pdf', $factura->id) }}" class="btn btn-info" target="_blank"><i class="bx bx-file me-1"></i> PDF</a>
              <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEnviarEmailFactura{{ $factura->id }}"><i class="bx bx-envelope me-1"></i> Email</button>
                            @can('delete', $factura)
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalAnularFactura{{ $factura->id }}"><i class="bx bx-x-circle me-1"></i> Anular</button>
                            @endcan
                            @else
              <span class="badge bg-label-danger fs-6">Factura Anulada</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

    <!-- Estado de Firma y Emisión, Estado SRI, Datos SRI, Info Sistema -->
    <div class="col-lg-4">
      <!-- Datos SRI -->
      <div class="card mb-4">
        <div class="card-header bg-label-primary">
          <h5 class="mb-0"><i class="bx bx-file me-2"></i> Datos Electrónicos</h5>
                </div>
                <div class="card-body">
          <div class="mb-2 d-flex justify-content-between"><span class="text-muted">Secuencial:</span><span class="fw-bold">{{ $factura->getNumeroFormateado() }}</span></div>
          <div class="mb-2 d-flex justify-content-between"><span class="text-muted">CUA:</span><span class="fw-bold">{{ $factura->getCUAFormateado() }}</span></div>
          <div class="mb-2 d-flex justify-content-between"><span class="text-muted">Ambiente:</span><span>{{ $factura->ambiente ?? 'PRODUCCION' }}</span></div>
          <div class="mb-2 d-flex justify-content-between"><span class="text-muted">Tipo Emisión:</span><span>{{ $factura->tipo_emision ?? 'NORMAL' }}</span></div>
          <div class="d-flex justify-content-between"><span class="text-muted">Forma de Pago:</span><span>{{ $factura->forma_pago ?? 'EFECTIVO' }}</span></div>
                </div>
            </div>
            <!-- Información del Sistema -->
      <div class="card mb-4">
        <div class="card-header bg-label-secondary">
          <h5 class="mb-0"><i class="bx bx-cog me-2"></i> Información del Sistema</h5>
                </div>
                <div class="card-body">
          <div class="mb-2 d-flex justify-content-between"><span class="text-muted">ID de la Factura:</span><span class="fw-bold">#{{ $factura->id }}</span></div>
          <div class="mb-2 d-flex justify-content-between"><span class="text-muted">Fecha de Creación:</span><span>{{ $factura->created_at->format('d/m/Y H:i') }}</span></div>
                        @if($factura->updated_at != $factura->created_at)
            <div class="mb-2 d-flex justify-content-between"><span class="text-muted">Última Actualización:</span><span>{{ $factura->updated_at->format('d/m/Y H:i') }}</span></div>
                        @endif
                        @if($factura->trashed())
            <div class="d-flex justify-content-between"><span class="text-muted">Fecha de Anulación:</span><span>{{ $factura->deleted_at->format('d/m/Y H:i') }}</span></div>
                        @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Datos del Emisor -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-warning shadow-lg">
                <div class="card-header bg-gradient-warning text-white">
          <h4 class="card-title mb-0"><i class="bx bx-buildings me-2"></i> Datos del Emisor (SowarTech)</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
              <div class="mb-2"><span class="text-muted">RUC:</span><span class="fw-bold ms-2">1728167857001</span></div>
                        </div>
                        <div class="col-md-3">
              <div class="mb-2"><span class="text-muted">Razón Social:</span><span class="fw-bold ms-2">SowarTech</span></div>
                        </div>
                        <div class="col-md-3">
              <div class="mb-2"><span class="text-muted">Dirección:</span><span class="ms-2">Quito, El Condado, Pichincha</span></div>
                        </div>
                        <div class="col-md-3">
              <div class="mb-2"><span class="text-muted">Tipo Emisor:</span><span class="ms-2">RUC</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Productos -->
    <div class="row">
        <div class="col-12">
      <div class="card">
        <div class="card-header bg-label-primary d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i> Detalles de Productos</h5>
          <span class="badge bg-label-info">{{ $factura->detalles->count() }} productos</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Precio Unitario</th>
                                    <th class="text-center">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($factura->detalles as $index => $detalle)
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($detalle->producto && $detalle->producto->imagen)
                          <img src="{{ asset('storage/productos/' . $detalle->producto->imagen) }}" alt="{{ $detalle->producto->nombre }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                          <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bx bx-box text-muted"></i></div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</div>
                                                @if($detalle->producto)
                                                <small class="text-muted">{{ $detalle->producto->descripcion }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                    <td class="text-center"><span class="badge bg-label-info fs-6">{{ $detalle->cantidad }}</span></td>
                    <td class="text-center"><span class="fw-bold">${{ number_format($detalle->precio_unitario, 2) }}</span></td>
                    <td class="text-center"><span class="fw-bold text-success">${{ number_format($detalle->subtotal, 2) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-center fw-bold">${{ number_format($factura->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-primary">IVA (15%):</td>
                                    <td class="text-center fw-bold text-primary">${{ number_format($factura->iva, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="4" class="text-end fw-bold fs-5">Total:</td>
                                    <td class="text-center fw-bold fs-5 text-success">${{ number_format($factura->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- Información SRI Compacta y QR -->
    @if($factura->tieneDatosSRI())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-lg border-0" style="background: #f8f9fa;">
                <div class="card-header" style="background: linear-gradient(90deg, #232c47 0%, #3b5998 100%); color: #fff; border-radius: 8px 8px 0 0;">
            <h5 class="mb-0"><i class="bx bx-shield me-2"></i> Verificación de Integridad SRI</h5>
                </div>
                <div class="card-body pb-2">
                    <div class="row g-3 align-items-stretch">
                        <!-- QR -->
                        <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
                <h6 class="text-primary mb-2" style="font-weight: bold; font-size: 0.9rem;">Código QR</h6>
                            @if($factura->imagen_qr)
                                <div class="bg-white p-2 rounded border shadow-sm mb-1" style="max-width: 150px;">
                                        <img src="data:image/png;base64,{{ $factura->imagen_qr }}" alt="QR Factura SRI" style="width: 120px; height: 120px; display: block; margin: 0 auto;" />
                                        <span class="small text-muted" style="font-size: 0.7rem;">Escanee para verificar</span>
                                </div>
                            @else
                                <div class="bg-white p-2 rounded border shadow-sm mb-1 text-center" style="max-width: 150px;">
                    <i class="bx bx-qr-scan" style="font-size: 3rem; color: #007bff;"></i>
                                    <div class="text-danger mt-1" style="font-size: 0.7rem;">QR no disponible</div>
                                </div>
                            @endif
                        </div>
                        <!-- Datos SRI -->
                        <div class="col-md-6 d-flex flex-column justify-content-center">
                            <div class="bg-white p-3 rounded border shadow-sm">
                                <div class="row">
                                    <div class="col-6">
                      <div class="mb-2"><span class="text-muted small">Secuencial:</span><br><span class="fw-bold" style="font-size: 1rem; letter-spacing: 1px;">{{ $factura->getNumeroFormateado() }}</span></div>
                      <div class="mb-2"><span class="text-muted small">CUA:</span><br><span class="fw-bold" style="font-size: 0.8rem;">{{ $factura->getCUAFormateado() }}</span></div>
                      <div class="mb-2"><span class="text-muted small">Ambiente:</span><span class="fw-bold text-primary ms-2">{{ $factura->ambiente ?? 'PRODUCCION' }}</span></div>
                                    </div>
                                    <div class="col-6">
                      <div class="mb-2"><span class="text-muted small">Estado:</span><span class="badge bg-{{ $factura->getEstadoAutorizacion() === 'AUTORIZADO' ? 'success' : ($factura->getEstadoAutorizacion() === 'PROCESANDO' ? 'warning' : 'info') }} ms-2">{{ $factura->getEstadoAutorizacion() }}</span></div>
                      <div class="mb-2"><span class="text-muted small">Fecha:</span><span class="fw-bold ms-2">{{ $factura->fecha_emision ? $factura->fecha_emision->format('d/m/Y') : $factura->created_at->format('d/m/Y') }}</span></div>
                      <div class="mb-2"><span class="text-muted small">Hora:</span><span class="fw-bold ms-2">{{ $factura->hora_emision ?? $factura->created_at->format('H:i') }}</span></div>
                      <div class="mb-2"><span class="text-muted small">Pago:</span><span class="fw-bold ms-2">{{ $factura->forma_pago ?? 'EFECTIVO' }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Firma Digital -->
                        <div class="col-md-3 d-flex flex-column align-items-center justify-content-center">
                <h6 class="text-primary mb-2" style="font-weight: bold; font-size: 0.9rem;">Firma Digital</h6>
                            <div class="w-100 p-2 rounded border shadow-sm" style="background: #fff; min-height: 100px;">
                                @if($factura->isFirmada())
                                    <div class="d-flex flex-column align-items-center">
                      <span class="badge bg-success mb-1" style="font-size: 0.8rem;"><i class="bx bx-shield me-1"></i>VÁLIDA</span>
                                        <span class="text-success small">Firma digital válida</span>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center">
                      <span class="badge bg-warning mb-1" style="font-size: 0.8rem;"><i class="bx bx-time me-1"></i>PENDIENTE</span>
                                        <span class="text-warning small">Requiere firma digital</span>
                                    </div>
                                @endif
                                <div class="mt-2" style="font-size: 8px; word-break: break-all; background: #f8f9fa; padding: 4px; border-radius: 3px; border: 1px solid #dee2e6;">
                    <strong>Firma:</strong><br>{{ Str::limit($factura->firma_digital ?? 'No disponible', 50) }}
        </div>
    </div>
</div>
                    </div>
                    <!-- Contenido QR -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="bg-light p-2 rounded border shadow-sm">
                  <h6 class="mb-1 text-primary" style="font-weight: bold; font-size: 0.8rem;"><i class="bx bx-qr-scan me-1"></i> Contenido del Código QR</h6>
                                <div style="overflow-x: auto;">
                    <code class="small text-break" style="font-size: 9px; word-break: break-all; white-space: pre;">{{ $factura->contenido_qr }}</code>
                                </div>
                            </div>
                        </div>
                </div>
                    <!-- Leyenda legal -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="bg-warning bg-opacity-25 p-2 rounded border border-warning text-center small" style="font-size: 10px;">
                                <strong>AVISO:</strong> Esta factura electrónica ha sido generada por el Sistema de Rentas Internas del Ecuador. La firma digital y el código QR garantizan la autenticidad e integridad del documento.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- MODAL ENVIAR EMAIL -->
<div class="modal fade" id="modalEnviarEmailFactura{{ $factura->id }}" tabindex="-1" aria-labelledby="modalEnviarEmailLabel{{ $factura->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('facturas.send-email', $factura->id) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalEnviarEmailLabel{{ $factura->id }}">
            <i class="bx bx-envelope text-success me-2"></i> Enviar Factura por Email
                    </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
                <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Email destino</label>
            <input type="email" name="email" class="form-control" value="{{ $factura->cliente->email ?? '' }}" required>
                    </div>
                            <div class="mb-3">
            <label class="form-label">Asunto</label>
            <input type="text" name="asunto" class="form-control" value="Factura electrónica #{{ $factura->getNumeroFormateado() }}" required maxlength="255">
                        </div>
                    <div class="mb-3">
            <label class="form-label">Mensaje (opcional)</label>
            <textarea name="mensaje" class="form-control" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="bx bx-send me-1"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ANULAR FACTURA -->
@can('delete', $factura)
<div class="modal fade" id="modalAnularFactura{{ $factura->id }}" tabindex="-1" aria-labelledby="modalAnularFacturaLabel{{ $factura->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
      <form method="POST" action="{{ route('facturas.destroy', $factura->id) }}" id="formAnularFactura{{ $factura->id }}">
        @csrf
        @method('DELETE')
            <div class="modal-header">
          <h5 class="modal-title" id="modalAnularFacturaLabel{{ $factura->id }}">
            <i class="bx bx-x-circle text-danger me-2"></i> Anular Factura
                </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
                <div class="modal-body">
                    <div class="mb-3">
            <label class="form-label">Contraseña de Administrador</label>
            <div class="input-group">
              <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
              <button class="btn btn-outline-secondary toggle-password" type="button">
                <i class="bx bx-hide"></i>
              </button>
            </div>
                    </div>
                    <div class="mb-3">
            <label class="form-label">Motivo de Anulación</label>
            <select name="observacion" class="form-select" required>
              <option value="">Seleccione un motivo</option>
              <option value="Error en la facturación">Error en la facturación</option>
              <option value="Solicitud del cliente">Solicitud del cliente</option>
              <option value="Producto no disponible">Producto no disponible</option>
              <option value="Problema de pago">Problema de pago</option>
              <option value="Datos incorrectos">Datos incorrectos</option>
              <option value="Duplicado">Duplicado</option>
              <option value="Otro">Otro</option>
            </select>
                    </div>
                    <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="confirm_delete" id="confirmAnularFactura{{ $factura->id }}" required>
              <label class="form-check-label" for="confirmAnularFactura{{ $factura->id }}">
                Confirmo que deseo anular esta factura
              </label>
            </div>
          </div>
          <div class="alert alert-danger">
            <i class="bx bx-error-circle me-2"></i>
            <strong>¡Advertencia!</strong> Esta acción anulará la factura y revertirá el stock de los productos vendidos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger" disabled>
            <i class="bx bx-x-circle me-1"></i> Anular Factura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('styles')
<style>
/* Inspirado en users/show.blade.php */
.page-title {
  margin-bottom: 2rem;
}
.card {
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.04);
  margin-bottom: 2rem;
  background: #fff;
}
.card-header {
  background: #f8f9fa;
  color: #232c47;
  border-radius: 16px 16px 0 0;
  padding: 1.25rem 2rem;
  font-size: 1.15rem;
  font-weight: 600;
  border-bottom: 1px solid #e2e8f0;
}
.card-body {
  padding: 2rem;
}
.badge {
  font-size: 0.9rem;
  padding: 0.45em 0.9em;
  border-radius: 0.7em;
}
.table th {
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
  color: #232c47;
}
.table td {
  vertical-align: middle;
  border-bottom: 1px solid #f1f3f4;
}
.table tbody tr:hover {
  background-color: #f0f4fa;
}
.btn {
  border-radius: 0.5rem;
  font-weight: 500;
}
.btn-warning, .btn-info, .btn-success, .btn-danger {
  color: #fff !important;
}
.btn-warning { background: #ffb300; border: none; }
.btn-info { background: #1976d2; border: none; }
.btn-success { background: #43a047; border: none; }
.btn-danger { background: #e53935; border: none; }
.btn-warning:hover { background: #ffa000; }
.btn-info:hover { background: #1565c0; }
.btn-success:hover { background: #388e3c; }
.btn-danger:hover { background: #c62828; }
@media (max-width: 768px) {
  .card-body { padding: 1rem; }
  .card-header { padding: 1rem; font-size: 1rem; }
}
/* Notificaciones toast */
#notification-container {
  z-index: 9999;
}
.alert {
  border: none;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  animation: slideInRight 0.3s ease-out;
}
.alert-success {
  background: #198754;
  color: white;
}
.alert-error {
  background: #dc3545;
  color: white;
}
.alert-warning {
  background: #ffc107;
  color: #212529;
}
.alert-info {
  background: #0dcaf0;
  color: white;
}
</style>
@endpush

{{-- Contenedor para notificaciones visuales tipo toast --}}
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div> 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (window.facturasManager) {
    @if(session('success'))
      window.facturasManager.showSuccess(@json(session('success')));
    @endif
    @if(session('error'))
      window.facturasManager.showError(@json(session('error')));
    @endif
    @if(session('info'))
      window.facturasManager.showInfo(@json(session('info')));
    @endif
    @if($errors->any())
      @foreach($errors->all() as $error)
        window.facturasManager.showError(@json($error));
      @endforeach
    @endif
  }
});
</script>
@endpush 