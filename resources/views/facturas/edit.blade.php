@extends('layouts.app')

@section('title', 'Gestión de Factura')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('facturas.index') }}"><i class="bi bi-receipt"></i> Facturas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('facturas.show', $factura) }}"><i class="bi bi-eye"></i> Factura #{{ $factura->getNumeroFormateado() }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-gear"></i> Gestión</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal de la Factura -->
        <div class="col-lg-8">
            <!-- Mensaje Informativo -->
            <div class="alert alert-info border-0 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle fs-4 me-3"></i>
                    <div>
                        <h6 class="mb-1">Vista de Gestión de Factura Electrónica</h6>
                        <p class="mb-0">
                            Esta es una vista de gestión que permite realizar acciones sobre la factura electrónica. 
                            <strong>Las facturas no se pueden editar por razones contables y legales del SRI.</strong>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card card-outline card-info shadow-lg">
                <div class="card-header bg-gradient-info text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i> 
                        Gestión de Factura Electrónica #{{ $factura->getNumeroFormateado() }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('facturas.show', $factura) }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-eye me-1"></i> Ver Factura
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Información de la Factura -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item mb-4">
                                <label class="form-label fw-bold text-muted">
                                    <i class="bi bi-person me-2 text-info"></i>Cliente
                                </label>
                                <p class="form-control-plaintext fs-5 fw-semibold">
                                    {{ $factura->cliente->nombre ?? 'Cliente eliminado' }}
                                </p>
                            </div>
                            
                            <div class="info-item mb-4">
                                <label class="form-label fw-bold text-muted">
                                    <i class="bi bi-person-badge me-2 text-info"></i>Vendedor
                                </label>
                                <p class="form-control-plaintext fs-5">
                                    {{ $factura->usuario->name ?? 'Usuario eliminado' }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-item mb-4">
                                <label class="form-label fw-bold text-muted">
                                    <i class="bi bi-calendar me-2 text-info"></i>Fecha de Emisión
                                </label>
                                <p class="form-control-plaintext fs-5">
                                    {{ $factura->fecha_emision ? $factura->fecha_emision->format('d/m/Y') : $factura->created_at->format('d/m/Y') }}
                                </p>
                                @if($factura->hora_emision)
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $factura->hora_emision }}
                                </small>
                                @endif
                            </div>
                            
                            <div class="info-item mb-4">
                                <label class="form-label fw-bold text-muted">
                                    <i class="bi bi-currency-dollar me-2 text-info"></i>Total
                                </label>
                                <p class="form-control-plaintext fs-4 fw-bold text-success">
                                    ${{ number_format($factura->total, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado SRI -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-{{ $factura->getEstadoAutorizacion() === 'AUTORIZADO' ? 'success' : ($factura->getEstadoAutorizacion() === 'PROCESANDO' ? 'warning' : 'info') }} border-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-{{ $factura->getEstadoAutorizacion() === 'AUTORIZADO' ? 'check-circle' : ($factura->getEstadoAutorizacion() === 'PROCESANDO' ? 'hourglass-split' : 'clock') }} fs-4 me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Estado de Autorización SRI</h6>
                                        <p class="mb-0">
                                            <span class="badge bg-{{ $factura->getEstadoAutorizacion() === 'AUTORIZADO' ? 'success' : ($factura->getEstadoAutorizacion() === 'PROCESANDO' ? 'warning' : 'info') }} fs-6">
                                                {{ $factura->getEstadoAutorizacion() }}
                                            </span>
                                            @if($factura->getEstadoAutorizacion() === 'AUTORIZADO')
                                                <small class="text-muted ms-2">Esta factura ha sido autorizada por el SRI y es válida para efectos contables.</small>
                                            @elseif($factura->getEstadoAutorizacion() === 'PROCESANDO')
                                                <small class="text-muted ms-2">Esta factura está siendo procesada por el SRI.</small>
                                            @else
                                                <small class="text-muted ms-2">Esta factura está pendiente de autorización por el SRI.</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos SRI -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="bi bi-file-earmark-text me-2"></i> Datos Electrónicos SRI
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item d-flex justify-content-between mb-2">
                                                <span class="text-muted">Número Secuencial:</span>
                                                <span class="fw-bold">{{ $factura->getNumeroFormateado() }}</span>
                                            </div>
                                            <div class="info-item d-flex justify-content-between mb-2">
                                                <span class="text-muted">CUA:</span>
                                                <span class="fw-bold">{{ $factura->getCUAFormateado() }}</span>
                                            </div>
                                            <div class="info-item d-flex justify-content-between mb-2">
                                                <span class="text-muted">Ambiente:</span>
                                                <span>{{ $factura->ambiente ?? 'PRODUCCION' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item d-flex justify-content-between mb-2">
                                                <span class="text-muted">Tipo Emisión:</span>
                                                <span>{{ $factura->tipo_emision ?? 'NORMAL' }}</span>
                                            </div>
                                            <div class="info-item d-flex justify-content-between mb-2">
                                                <span class="text-muted">Forma de Pago:</span>
                                                <span>{{ $factura->forma_pago ?? 'EFECTIVO' }}</span>
                                            </div>
                                            <div class="info-item d-flex justify-content-between mb-2">
                                                <span class="text-muted">Tipo Documento:</span>
                                                <span>{{ $factura->tipo_documento ?? 'FACTURA' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Productos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="bi bi-list-ul me-2"></i> Resumen de Productos
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Precio Unit.</th>
                                                    <th class="text-center">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($factura->detalles as $detalle)
                                                <tr>
                                                    <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                                    <td class="text-center">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                                    <td class="text-center">${{ number_format($detalle->subtotal, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                                    <td class="text-center fw-bold">${{ number_format($factura->subtotal, 2) }}</td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td colspan="3" class="text-end fw-bold text-primary">IVA (15%):</td>
                                                    <td class="text-center fw-bold text-primary">${{ number_format($factura->iva, 2) }}</td>
                                                </tr>
                                                <tr class="table-success">
                                                    <td colspan="3" class="text-end fw-bold fs-5">Total:</td>
                                                    <td class="text-center fw-bold fs-5 text-success">${{ number_format($factura->total, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Sistema -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong><i class="bi bi-calendar me-1"></i> Creada:</strong><br>
                                        <small>{{ $factura->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="bi bi-clock me-1"></i> Última Actualización:</strong><br>
                                        <small>{{ $factura->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="bi bi-box me-1"></i> Productos:</strong><br>
                                        <small>{{ $factura->detalles->count() }} productos</small>
                                    </div>
                                    <div class="col-md-3">
                                        <strong><i class="bi bi-shield-check me-1"></i> Estado SRI:</strong><br>
                                        <span class="badge bg-{{ $factura->getEstadoAutorizacion() === 'AUTORIZADO' ? 'success' : ($factura->getEstadoAutorizacion() === 'PROCESANDO' ? 'warning' : 'info') }}">
                                            {{ $factura->getEstadoAutorizacion() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Acciones -->
        <div class="col-lg-4">
            <!-- Estado de Autorización SRI -->
            <div class="card card-outline card-success shadow-lg mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i> Estado SRI
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @if($factura->getEstadoAutorizacion() === 'AUTORIZADO')
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-success">Autorizado</h5>
                            <p class="text-muted">Factura autorizada por el SRI</p>
                        @elseif($factura->getEstadoAutorizacion() === 'PROCESANDO')
                            <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-warning">Procesando</h5>
                            <p class="text-muted">Enviando al SRI para autorización</p>
                        @else
                            <i class="bi bi-clock text-info" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-info">Pendiente</h5>
                            <p class="text-muted">Esperando envío al SRI</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Acciones Disponibles -->
            <div class="card card-outline card-primary shadow-lg mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i> Acciones Disponibles
                    </h5>
                </div>
                <div class="card-body acciones-disponibles">
                    <div class="d-grid gap-3">
                        <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-pdf me-2"></i> Descargar PDF
                        </a>

                        <a href="{{ route('facturas.show', $factura) }}" class="btn btn-outline-info">
                            <i class="bi bi-eye me-2"></i> Ver Detalles
                        </a>
                        
                        @can('update', $factura)

                        @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-lock me-2"></i>
                            <strong>Restricción:</strong> Solo el emisor de la factura puede anularla
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Información de Permisos -->
            <div class="card card-outline card-info shadow-lg">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-lock me-2"></i> Permisos de Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Emisor de la Factura:</span>
                            <span class="fw-bold">{{ $factura->usuario->name ?? 'Usuario eliminado' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Usuario Actual:</span>
                            <span class="fw-bold">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Puede Editar:</span>
                            @can('update', $factura)
                                <span class="badge bg-success">Sí</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endcan
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Puede Anular:</span>
                            @can('delete', $factura)
                                <span class="badge bg-success">Sí</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endcan
                        </div>
                        <div class="info-item d-flex justify-content-between">
                            <span class="text-muted">Puede Eliminar Permanentemente:</span>
                            @can('forceDelete', $factura)
                                <span class="badge bg-success">Sí</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos del Emisor -->
            <div class="card card-outline card-warning shadow-lg">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building me-2"></i> Datos del Emisor
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">RUC:</span>
                            <span class="fw-bold">1728167857001</span>
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Razón Social:</span>
                            <span class="fw-bold">SowarTech</span>
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Dirección:</span>
                            <span>Quito, El Condado, Pichincha</span>
                        </div>
                        <div class="info-item d-flex justify-content-between">
                            <span class="text-muted">Tipo Emisor:</span>
                            <span>RUC</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Anular Factura -->
<div class="modal fade" id="modalAnularFactura" tabindex="-1" aria-labelledby="modalAnularFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-warning text-white">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle display-6"></i>
                    <h5 class="modal-title mb-0" id="modalAnularFacturaLabel">Anular Factura Electrónica</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Al anular esta factura electrónica:
                </div>
                <ul class="list-unstyled">
                    <li><i class="bi bi-arrow-right text-warning me-2"></i>El stock de los productos se revertirá automáticamente</li>
                    <li><i class="bi bi-arrow-right text-warning me-2"></i>La factura ya no será válida para efectos contables</li>
                    <li><i class="bi bi-arrow-right text-warning me-2"></i>Se enviará notificación al SRI sobre la anulación</li>
                    <li><i class="bi bi-arrow-right text-warning me-2"></i>Esta acción quedará registrada en el historial de auditoría</li>
                </ul>
                <p class="mb-3 fw-bold">¿Estás seguro de que deseas anular la factura #{{ $factura->getNumeroFormateado() }}?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <form method="POST" action="{{ route('facturas.destroy', $factura) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="password" id="passwordAnular" required>
                    <input type="hidden" name="observacion" id="observacionAnular" required>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i> Anular Factura
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para enviar email -->
<div class="modal fade" id="modalEnviarEmail" tabindex="-1" aria-labelledby="modalEnviarEmailLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEnviarEmailLabel">
                    <i class="bi bi-envelope me-2"></i> Enviar Factura por Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('facturas.send-email', $factura) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email del destinatario:</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ $factura->cliente->email ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="asunto" class="form-label">Asunto:</label>
                        <input type="text" class="form-control" id="asunto" name="asunto" 
                               value="Factura #{{ $factura->getNumeroFormateado() }} - SowarTech" required>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje" class="form-label">Mensaje:</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="4">Adjunto la factura #{{ $factura->getNumeroFormateado() }} por un total de ${{ number_format($factura->total, 2) }}.

Gracias por su compra.

Saludos cordiales,
Equipo de SowarTech</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send me-1"></i> Enviar Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card-outline.card-info {
    border-top: 3px solid #17a2b8;
}

.card-outline.card-success {
    border-top: 3px solid #28a745;
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.card-outline.card-warning {
    border-top: 3px solid #ffc107;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.info-item {
    margin-bottom: 1rem;
}

.info-list .info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-list .info-item:last-child {
    border-bottom: none;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

@media (max-width: 768px) {
    .card-footer .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
@endsection
