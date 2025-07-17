@extends('layouts.app')

@section('title', 'Detalle de Cliente - ' . $cliente->nombre)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <h1 class="page-title mb-0">
                        <i class="bi bi-person-circle"></i> {{ $cliente->nombre }}
                    </h1>
                    <span class="badge badge-estado-{{ $cliente->estado }} fs-6">
                        {{ ucfirst($cliente->estado) }}
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarCliente">
                        <i class="bi bi-trash me-1"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Tarjeta Principal del Cliente -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-lg cliente-profile-card">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Avatar y Información Principal -->
                        <div class="col-lg-3 col-md-4 bg-gradient-primary text-white p-4 d-flex flex-column justify-content-center">
                            <div class="text-center">
                                <div class="cliente-avatar mb-3">
                                    <span>{{ strtoupper(substr($cliente->nombre,0,1)) }}</span>
                                </div>
                                <h3 class="mb-2">{{ $cliente->nombre }}</h3>
                                <p class="mb-3 opacity-75">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Cliente #{{ $cliente->id }}
                                </p>
                                <div class="cliente-status mb-3">
                                    @if($cliente->user_id && $cliente->user)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Usuario Activo
                                        </span>
                                    @elseif($cliente->password)
                                        <span class="badge bg-info">
                                            <i class="bi bi-person-check me-1"></i> Con Acceso
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Sin Acceso
                                        </span>
                                    @endif
                                </div>
                                <div class="cliente-meta">
                                    <small class="opacity-75">
                                        <i class="bi bi-calendar me-1"></i>
                                        Desde {{ $cliente->created_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información de Contacto -->
                        <div class="col-lg-9 col-md-8 p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="contact-info">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-envelope me-2"></i>Información de Contacto
                                        </h5>
                                        
                                        <div class="info-item mb-3">
                                            <label class="form-label text-muted small mb-1">
                                                <i class="bi bi-envelope me-1"></i>Email
                                            </label>
                                            <div class="d-flex align-items-center">
                                                <a href="mailto:{{ $cliente->email }}" class="text-decoration-none fw-semibold">
                                                    {{ $cliente->email }}
                                                </a>
                                                <button class="btn btn-sm btn-outline-primary ms-2" onclick="copiarAlPortapapeles('{{ $cliente->email }}')" title="Copiar email">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="info-item mb-3">
                                            <label class="form-label text-muted small mb-1">
                                                <i class="bi bi-telephone me-1"></i>Teléfono
                                            </label>
                                            <div class="d-flex align-items-center">
                                                @if($cliente->telefono)
                                                    <a href="tel:{{ $cliente->telefono }}" class="text-decoration-none fw-semibold">
                                                        {{ $cliente->telefono }}
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-success ms-2" onclick="copiarAlPortapapeles('{{ $cliente->telefono }}')" title="Copiar teléfono">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">No especificado</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <label class="form-label text-muted small mb-1">
                                                <i class="bi bi-geo-alt me-1"></i>Dirección
                                            </label>
                                            <p class="mb-0 fw-semibold">
                                                @if($cliente->direccion)
                                                    {{ $cliente->direccion }}
                                                @else
                                                    <span class="text-muted">No especificada</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="system-info">
                                        <h5 class="text-primary mb-3">
                                            <i class="bi bi-gear me-2"></i>Información del Sistema
                                        </h5>
                                        
                                        <div class="info-item mb-3">
                                            <label class="form-label text-muted small mb-1">Estado</label>
                                            <span class="badge badge-estado-{{ $cliente->estado }} fs-6">
                                                {{ ucfirst($cliente->estado) }}
                                            </span>
                                        </div>
                                        
                                        <div class="info-item mb-3">
                                            <label class="form-label text-muted small mb-1">Usuario ID</label>
                                            <span class="fw-semibold">
                                                @if($cliente->user_id)
                                                    #{{ $cliente->user_id }}
                                                @else
                                                    <span class="text-muted">No vinculado</span>
                                                @endif
                                            </span>
                                        </div>
                                        
                                        <div class="info-item mb-3">
                                            <label class="form-label text-muted small mb-1">Última Actualización</label>
                                            <span class="fw-semibold">
                                                {{ $cliente->updated_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        
                                        <div class="info-item">
                                            <label class="form-label text-muted small mb-1">Creado por</label>
                                            <span class="fw-semibold">
                                                @if($cliente->created_by)
                                                    {{ \App\Models\User::find($cliente->created_by)->name ?? 'Sistema' }}
                                                @else
                                                    Sistema
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas y Métricas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card stat-card-primary">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-2">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="stat-number" data-value="{{ $cliente->facturas->count() }}">
                                {{ $cliente->facturas->count() }}
                            </div>
                            <div class="stat-label">Total Facturas</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card stat-card-success">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-2">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="stat-number" data-value="{{ $cliente->facturas->sum('total') }}">
                                ${{ number_format($cliente->facturas->sum('total'), 2) }}
                            </div>
                            <div class="stat-label">Total Comprado</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card stat-card-info">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-2">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-number" data-value="{{ $cliente->facturas->where('estado', 'activa')->count() }}">
                                {{ $cliente->facturas->where('estado', 'activa')->count() }}
                            </div>
                            <div class="stat-label">Facturas Activas</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card stat-card-warning">
                        <div class="card-body text-center">
                            <div class="stat-icon mb-2">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="stat-number" data-value="{{ $cliente->facturas->where('estado', 'cancelada')->count() }}">
                                {{ $cliente->facturas->where('estado', 'cancelada')->count() }}
                            </div>
                            <div class="stat-label">Facturas Canceladas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Facturas del Cliente -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary shadow-lg">
                <div class="card-header bg-white border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-receipt me-2"></i> 
                            Facturas de {{ $cliente->nombre }}
                        </h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-6">
                                {{ $cliente->facturas->count() }} facturas
                            </span>
                            @if($cliente->facturas->count() > 0)
                                <a href="{{ route('facturas.create') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i> Nueva Factura
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($cliente->facturas->count() > 0)
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="min-width: 100px;"># Factura</th>
                                    <th style="min-width: 150px;">Fecha</th>
                                    <th class="text-center" style="min-width: 120px;">Total</th>
                                    <th class="text-center" style="min-width: 100px;">Estado</th>
                                    <th class="text-center" style="min-width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->facturas->sortByDesc('created_at') as $factura)
                                <tr class="table-row-animate">
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6">#{{ $factura->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $factura->created_at->format('d/m/Y') }}</span>
                                            <small class="text-muted">{{ $factura->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success fs-6">${{ number_format($factura->total, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-estado-{{ $factura->estado }}">
                                            {{ ucfirst($factura->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('facturas.show', $factura) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Ver Factura">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('facturas.edit', $factura) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar Factura">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('facturas.pdf', $factura) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Descargar PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="card-body text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Sin Facturas</h5>
                        <p class="text-muted mb-4">Este cliente aún no tiene facturas registradas.</p>
                        <a href="{{ route('facturas.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Crear Primera Factura
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Cliente -->
<div class="modal fade" id="modalEliminarCliente" tabindex="-1" aria-labelledby="modalEliminarClienteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0">
      <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="modal-form" data-modal-id="modalEliminarCliente">
        @csrf
        @method('DELETE')
        <div class="modal-header bg-danger text-white align-items-center">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-trash display-5"></i>
            <h5 class="modal-title mb-0" id="modalEliminarClienteLabel">Eliminar Cliente</h5>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <!-- Errores de validación -->
          @if ($errors->any() && session('modal') == 'eliminar-'.$cliente->id)
            <div class="alert alert-danger border-0 shadow-sm mb-4">
              <div class="d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
                <div>
                  <h6 class="alert-heading mb-1"><strong>Errores de Validación</strong></h6>
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          @endif
          <!-- Alerta de advertencia -->
          <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
              <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
              <div>
                <h6 class="alert-heading mb-1"><strong>¡ACCIÓN IRREVERSIBLE!</strong></h6>
                <p class="mb-0">Esta acción eliminará temporalmente al cliente del sistema. Podrás restaurarlo más tarde desde la sección de eliminados.</p>
              </div>
            </div>
          </div>
          <!-- Reporte del cliente -->
          <div class="card border-0 bg-light mb-4">
            <div class="card-header bg-white border-bottom">
              <h6 class="mb-0"><i class="bi bi-file-earmark-person me-2"></i>Información del Cliente</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label text-muted small">ID del Cliente</label>
                    <p class="mb-0 fw-bold">{{ $cliente->id }}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label text-muted small">Nombre Completo</label>
                    <p class="mb-0 fw-bold">{{ $cliente->nombre }}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label text-muted small">Email</label>
                    <p class="mb-0">{{ $cliente->email }}</p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label text-muted small">Teléfono</label>
                    <p class="mb-0">{{ $cliente->telefono }}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label text-muted small">Dirección</label>
                    <p class="mb-0">{{ $cliente->direccion }}</p>
                  </div>
                  <div class="mb-3">
                    <label class="form-label text-muted small">Estado</label>
                    <p class="mb-0"><span class="badge bg-success">{{ ucfirst($cliente->estado) }}</span></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <p class="mb-3 fw-bold">¿Estás seguro que deseas eliminar a este cliente?</p>
          <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña de administrador" required autocomplete="off" value="{{ old('password') }}">
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3 input-group">
            <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
            <select name="observacion" class="form-select @error('observacion') is-invalid @enderror" required>
              <option value="">Selecciona un motivo</option>
              <option value="Usuario bloqueado por comportamiento inadecuado" {{ old('observacion') == 'Usuario bloqueado por comportamiento inadecuado' ? 'selected' : '' }}>Usuario bloqueado por comportamiento inadecuado</option>
              <option value="Cliente solicitó la eliminación de su cuenta" {{ old('observacion') == 'Cliente solicitó la eliminación de su cuenta' ? 'selected' : '' }}>Cliente solicitó la eliminación de su cuenta</option>
              <option value="Datos duplicados o incorrectos" {{ old('observacion') == 'Datos duplicados o incorrectos' ? 'selected' : '' }}>Datos duplicados o incorrectos</option>
              <option value="Otro motivo" {{ old('observacion') == 'Otro motivo' ? 'selected' : '' }}>Otro motivo</option>
            </select>
            @error('observacion')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
          <button type="submit" class="btn btn-danger btn-lg"><i class="bi bi-trash"></i> Eliminar Cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* ===== MODALES SÚPER PROFESIONALES ===== */
.modal.fade.animated {
    animation-duration: 0.4s;
}
.fadeInDown {
    animation-name: fadeInDown;
}
.faster {
    animation-duration: 0.25s;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translate3d(0, -40px, 0); }
    to { opacity: 1; transform: none; }
}

/* Estilos súper profesionales para modales */
.modal {
    backdrop-filter: blur(8px);
    background: rgba(0, 0, 0, 0.4);
}

/* Mejoras para el botón de cerrar */
.btn-close {
    transition: all 0.3s ease;
}

.btn-close:hover {
    transform: scale(1.1);
}

/* Estilos para selects en modales */
.modal-body .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

/* Animaciones para elementos */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.animate__animated {
    animation-duration: 0.5s;
    animation-fill-mode: both;
}

.animate__pulse {
    animation-name: pulse;
}

/* Estilos para notificaciones */
.alert.position-fixed {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.alert-success {
    border-left: 4px solid #1abc9c;
}

.alert-success .bi-check-circle-fill {
    color: #1abc9c;
}

/* Estilos para indicadores de carga */
.loading-indicator {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Estilos para formularios de filtros */
.clientes-form input:focus,
.clientes-form select:focus,
.auditoria-form input:focus,
.auditoria-form select:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
}

/* Estilos para notificaciones de filtrado */
.filter-notification {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
    border-left: 4px solid #17a2b8;
}

.filter-notification .bi-funnel-fill {
    color: #17a2b8;
}

/* Estilos para paginación mejorada */
.pagination {
    gap: 0.25rem;
}

.pagination .page-link {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    color: #6c757d;
    transition: all 0.3s ease;
    font-weight: 500;
}

.pagination .page-link:hover {
    background-color: #696cff;
    border-color: #696cff;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
}

.pagination .page-item.active .page-link {
    background-color: #696cff;
    border-color: #696cff;
    color: white;
    box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
}

.pagination .page-item.disabled .page-link {
    color: #adb5bd;
    border-color: #e9ecef;
    background-color: #f8f9fa;
}

.modal-body .form-select:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
}

.modal-body .form-select.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.modal-body .input-group-text {
    border-radius: 8px 0 0 8px;
    border: 2px solid #e9ecef;
    border-right: none;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.modal-body .form-select {
    border-left: none;
    border-radius: 0 8px 8px 0;
}

.modal-dialog {
    max-width: 600px;
    margin: 1.75rem auto;
}

.modal-dialog.modal-lg {
    max-width: 800px;
}

.modal-content {
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafd 100%);
    position: relative;
}

.modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #696cff, #03c3ec, #1abc9c);
    z-index: 1;
}

/* Header del modal */
.modal-header {
    background: linear-gradient(135deg, #696cff 0%, #5350e3 100%);
    color: white;
    border: none;
    padding: 1.5rem 2rem;
    position: relative;
    overflow: hidden;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.modal-header.bg-success {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
}

.modal-header.bg-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #e64a4a 100%);
}

.modal-header.bg-dark {
    background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
}

.modal-header.bg-warning {
    background: linear-gradient(135deg, #ffab00 0%, #e6a100 100%);
}

.modal-title {
    font-weight: 700;
    font-size: 1.4rem;
    margin: 0;
    position: relative;
    z-index: 2;
}

.modal-header i {
    font-size: 2rem;
    margin-right: 0.75rem;
    position: relative;
    z-index: 2;
}

.modal-header .btn-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 1.5rem;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
    opacity: 1;
    filter: brightness(0) invert(1);
}

.modal-header .btn-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
    opacity: 1;
}

.modal-header .btn-close:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
}

/* Body del modal */
.modal-body {
    padding: 2rem;
    background: #ffffff;
}

/* Alertas mejoradas */
.alert {
    border-radius: 12px;
    border: none;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}

.alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: currentColor;
    opacity: 0.3;
}

.alert-success {
    background: linear-gradient(135deg, #e8f8f5 0%, #d1f2eb 100%);
    color: #1abc9c;
    border-left: 4px solid #1abc9c;
}

.alert-danger {
    background: linear-gradient(135deg, #ffeaea 0%, #ffd6d6 100%);
    color: #ff6b6b;
    border-left: 4px solid #ff6b6b;
}

.alert-dark {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #343a40;
    border-left: 4px solid #343a40;
}

.alert-warning {
    background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
    color: #ffab00;
    border-left: 4px solid #ffab00;
}

.alert i {
    font-size: 1.5rem;
    margin-right: 0.75rem;
}

.alert-heading {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

/* Cards dentro del modal */
.modal-body .card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.modal-body .card-header {
    background: linear-gradient(135deg, #f8fafd 0%, #e9ecef 100%);
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
    font-weight: 600;
    color: #495057;
}

.modal-body .card-body {
    padding: 1.5rem;
    background: #ffffff;
}

/* Formularios en modales */
.modal-body .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modal-body .form-control,
.modal-body .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #ffffff;
}

.modal-body .form-control:focus,
.modal-body .form-select:focus {
    border-color: #696cff;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.15);
    transform: translateY(-1px);
}

.modal-body .input-group-text {
    background: linear-gradient(135deg, #f8fafd 0%, #e9ecef 100%);
    border: 2px solid #e9ecef;
    border-right: none;
    color: #696cff;
    font-weight: 600;
}

.modal-body .input-group .form-control {
    border-left: none;
}

.modal-body .input-group .form-control:focus + .input-group-text,
.modal-body .input-group .form-control:focus {
    border-color: #696cff;
}

/* Footer del modal */
.modal-footer {
    background: linear-gradient(135deg, #f8fafd 0%, #e9ecef 100%);
    border: none;
    padding: 1.5rem 2rem;
    border-top: 1px solid #e9ecef;
}

.modal-footer .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.modal-footer .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.modal-footer .btn:hover::before {
    left: 100%;
}

.modal-footer .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    color: white;
    border-color: #6c757d;
}

.modal-footer .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(108, 117, 125, 0.3);
}

.modal-footer .btn-success {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
    color: white;
    border-color: #1abc9c;
}

.modal-footer .btn-success:hover {
    background: linear-gradient(135deg, #16a085 0%, #138d75 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(26, 188, 156, 0.3);
}

.modal-footer .btn-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #e64a4a 100%);
    color: white;
    border-color: #ff6b6b;
}

.modal-footer .btn-danger:hover {
    background: linear-gradient(135deg, #e64a4a 0%, #d63031 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(255, 107, 107, 0.3);
}

.modal-footer .btn-dark {
    background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
    color: white;
    border-color: #343a40;
}

.modal-footer .btn-dark:hover {
    background: linear-gradient(135deg, #23272b 0%, #1d2124 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(52, 58, 64, 0.3);
}

.modal-footer .btn-warning {
    background: linear-gradient(135deg, #ffab00 0%, #e6a100 100%);
    color: white;
    border-color: #ffab00;
}

.modal-footer .btn-warning:hover {
    background: linear-gradient(135deg, #e6a100 0%, #cc8f00 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(255, 171, 0, 0.3);
}

.modal-footer .btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    border-radius: 12px;
}

/* Animaciones adicionales */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal.fade .modal-dialog {
    animation: modalSlideIn 0.4s ease-out;
}

/* Responsive para modales */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-header {
        padding: 1rem 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        padding: 1rem 1.5rem;
    }
    
    .modal-title {
        font-size: 1.2rem;
    }
    
    .modal-header i {
        font-size: 1.5rem;
    }
}

/* Efectos hover para elementos del modal */
.modal-body .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.modal-body .alert:hover {
    transform: translateX(5px);
    transition: all 0.3s ease;
}

/* Badges mejorados en modales */
.modal-body .badge {
    border-radius: 8px;
    font-weight: 600;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modal-body .badge.bg-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #e64a4a 100%) !important;
    color: white;
}

.modal-body .badge.bg-success {
    background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%) !important;
    color: white;
}

.modal-body .badge.bg-warning {
    background: linear-gradient(135deg, #ffab00 0%, #e6a100 100%) !important;
    color: white;
}

.modal-body .badge.bg-info {
    background: linear-gradient(135deg, #03c3ec 0%, #0298b8 100%) !important;
    color: white;
}

/* Texto mejorado en modales */
.modal-body p.fw-bold {
    font-size: 1.1rem;
    color: #343a40;
    margin-bottom: 1rem;
}

.modal-body p.fw-bold.text-danger {
    color: #ff6b6b !important;
    font-weight: 700;
}

.modal-body .text-muted {
    color: #6c757d !important;
    font-size: 0.9rem;
}

/* Scroll personalizado para modales */
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #696cff #f8fafd;
}

.modal-body::-webkit-scrollbar {
    width: 6px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f8fafd;
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #696cff 0%, #03c3ec 100%);
    border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5350e3 0%, #0298b8 100%);
}

/* Estilos personalizados para la vista de detalle */
.cliente-profile-card {
    border-radius: 15px;
    overflow: hidden;
}

.cliente-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 auto;
    border: 3px solid rgba(255,255,255,0.3);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.stat-card {
    border-radius: 12px;
    border: none;
    transition: all 0.3s ease;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.stat-card-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
}

.stat-card-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.stat-card-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
}

.stat-card-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: white;
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.8;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.empty-state {
    padding: 2rem;
}

.table-row-animate {
    transition: all 0.2s ease;
}

/* Animaciones para las estadísticas */
@keyframes countUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.stat-card {
    animation: countUp 0.6s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

/* Responsive */
@media (max-width: 768px) {
    .cliente-profile-card .row {
        flex-direction: column;
    }
    
    .cliente-profile-card .col-lg-3 {
        order: -1;
    }
    
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
}

/* Notificaciones flotantes mejoradas */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 350px;
    max-width: 450px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border: none;
    overflow: hidden;
}

.notification::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(255,255,255,0.3);
}

.notification.success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border-left: 4px solid #155724;
}

.notification.error {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border-left: 4px solid #721c24;
}

.notification.info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    border-left: 4px solid #0c5460;
}

.notification.warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: #212529;
    border-left: 4px solid #856404;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%) scale(0.8);
        opacity: 0;
    }
    to {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0) scale(1);
        opacity: 1;
    }
    to {
        transform: translateX(100%) scale(0.8);
        opacity: 0;
    }
}

.notification-content {
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.notification-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
}

.notification-message {
    flex: 1;
    font-weight: 500;
    line-height: 1.4;
}

.notification-close {
    background: none;
    border: none;
    color: inherit;
    opacity: 0.7;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: opacity 0.2s ease;
}

.notification-close:hover {
    opacity: 1;
}

/* Responsive para notificaciones */
@media (max-width: 768px) {
    .notification {
        left: 10px;
        right: 10px;
        min-width: auto;
        max-width: none;
    }
}
</style>

<script>
// Función para copiar al portapapeles
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(function() {
        mostrarNotificacion('✅ Texto copiado al portapapeles exitosamente', 'success');
    }).catch(function(err) {
        console.error('Error al copiar: ', err);
        mostrarNotificacion('❌ Error al copiar al portapapeles', 'error');
    });
}

// Función para mostrar notificaciones mejoradas
function mostrarNotificacion(mensaje, tipo = 'info') {
    // Remover notificaciones existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notif => {
        notif.remove();
    });
    
    const notification = document.createElement('div');
    notification.className = `notification ${tipo}`;
    
    // Iconos según el tipo
    const icons = {
        'success': 'bi-check-circle-fill',
        'error': 'bi-exclamation-triangle-fill',
        'warning': 'bi-exclamation-triangle-fill',
        'info': 'bi-info-circle-fill'
    };
    
    notification.innerHTML = `
        <div class="notification-content">
            <i class="bi ${icons[tipo] || icons.info} notification-icon"></i>
            <div class="notification-message">${mensaje}</div>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 400);
        }
    }, 5000);
}

// Función para toggle de contraseña
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Animación de contadores
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar notificación de bienvenida
    setTimeout(() => {
        mostrarNotificacion('👋 Bienvenido al perfil de ' + '{{ $cliente->nombre }}', 'info');
    }, 1000);
    
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const finalValue = stat.getAttribute('data-value');
        const isCurrency = stat.textContent.includes('$');
        
        let currentValue = 0;
        const finalNumber = parseFloat(finalValue.replace(/[^\d.]/g, ''));
        const increment = finalNumber / 50;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalNumber) {
                if (isCurrency) {
                    stat.textContent = '$' + parseFloat(finalValue).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else {
                    stat.textContent = Math.floor(finalNumber);
                }
                clearInterval(timer);
            } else {
                if (isCurrency) {
                    stat.textContent = '$' + currentValue.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else {
                    stat.textContent = Math.floor(currentValue);
                }
            }
        }, 30);
    });
    
    // Manejo de formularios AJAX para modales
    const modalForms = document.querySelectorAll('.modal-form');
    modalForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const modalId = this.getAttribute('data-modal-id');
            const modal = document.getElementById(modalId);
            const submitBtn = modal.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('✅ ' + data.message, 'success');
                    
                    // Cerrar modal
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    // Mostrar errores en el modal
                    const errorContainer = modal.querySelector('.alert-danger');
                    if (errorContainer) {
                        errorContainer.remove();
                    }
                    
                    if (data.errors) {
                        const errorHtml = `
                            <div class="alert alert-danger border-0 shadow-sm mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1"><strong>Errores de Validación</strong></h6>
                                        <ul class="mb-0">
                                            ${Object.values(data.errors).flat().map(error => `<li>${error}</li>`).join('')}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                        modal.querySelector('.modal-body').insertAdjacentHTML('afterbegin', errorHtml);
                    }
                    
                    mostrarNotificacion('❌ ' + data.message, 'error');
                }
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('❌ Error al procesar la solicitud. Por favor, inténtalo de nuevo.', 'error');
                
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    });
});
</script>
@endsection
