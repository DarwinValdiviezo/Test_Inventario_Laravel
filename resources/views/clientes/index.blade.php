@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema /</span> Clientes
          </h4>
          <p class="text-muted mb-0">Gestión completa de clientes del sistema</p>
        </div>
        <div class="page-title-actions ms-auto">
          <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="bx bx-user-plus me-1"></i> Nuevo Cliente
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Filtros de Estado -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('clientes.index', array_merge(request()->except(['page','eliminados','log_buscar','log_accion','log_usuario','log_fecha_desde','log_fecha_hasta','log_per_page']), ['estado' => 'activo'])) }}" 
               class="btn btn-outline-primary {{ request('estado', 'activo') == 'activo' && !request('eliminados') ? 'active' : '' }}">
              <i class="bx bx-user-check me-1"></i> Activos
            </a>
            <a href="{{ route('clientes.index', array_merge(request()->except('page'), ['eliminados' => 1])) }}" 
               class="btn btn-outline-danger {{ request('eliminados') ? 'active' : '' }}">
              <i class="bx bx-trash me-1"></i> Eliminados
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filtros de Búsqueda -->
  @if(!request('eliminados'))
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-search me-1"></i> Filtros de Búsqueda
          </h5>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('clientes.index') }}" class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Buscar</label>
              <input type="text" name="buscar" value="{{ request('buscar') }}" 
                     class="form-control" placeholder="Nombre, email, teléfono...">
            </div>
            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Mostrar</label>
              <select name="per_page" class="form-select">
                @foreach([5,10,15,20,50] as $n)
                  <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>
                    {{ $n }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-search me-1"></i> Buscar
                </button>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                  <i class="bx bx-x me-1"></i> Limpiar
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  @else
  <!-- Tabla de Reporte/Auditoría para Eliminados -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-clipboard me-1"></i> Reporte de Auditoría de Eliminados
          </h5>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('clientes.index', ['eliminados' => 1]) }}" class="row g-3 mb-3 auditoria-form">
            <input type="hidden" name="eliminados" value="1">
            <div class="col-md-3">
              <label class="form-label">Buscar en Auditoría</label>
              <input type="text" name="log_buscar" value="{{ request('log_buscar') }}" class="form-control" placeholder="Descripción, observación, usuario...">
            </div>
            <div class="col-md-2">
              <label class="form-label">Acción</label>
              <select name="log_accion" class="form-select">
                <option value="">Todas las acciones</option>
                <option value="delete" {{ request('log_accion') == 'delete' ? 'selected' : '' }}>Eliminar</option>
                <option value="restore" {{ request('log_accion') == 'restore' ? 'selected' : '' }}>Restaurar</option>
                <option value="forceDelete" {{ request('log_accion') == 'forceDelete' ? 'selected' : '' }}>Borrar Definitivo</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Usuario</label>
              <select name="log_usuario" class="form-select">
                <option value="">Todos los usuarios</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" {{ request('log_usuario') == $usuario->id ? 'selected' : '' }}>
                  {{ $usuario->name }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Fecha Desde</label>
              <input type="date" name="log_fecha_desde" value="{{ request('log_fecha_desde') }}" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Fecha Hasta</label>
              <input type="date" name="log_fecha_hasta" value="{{ request('log_fecha_hasta') }}" class="form-control">
            </div>
            <div class="col-md-1">
              <label class="form-label">Mostrar</label>
              <select name="log_per_page" class="form-select">
                @foreach([5,10,15,20,50] as $n)
                <option value="{{ $n }}" {{ request('log_per_page', 10) == $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
              </select>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Acción</th>
                  <th>Cliente</th>
                  <th>Usuario</th>
                  <th>Observación</th>
                  <th>Fecha</th>
                  <th>Hace</th>
                </tr>
              </thead>
              <tbody>
                @forelse($logs as $log)
                <tr>
                  <td><span class="badge bg-{{ $log->action == 'delete' ? 'danger' : ($log->action == 'restore' ? 'success' : 'dark') }}">{{ ucfirst($log->action) }}</span></td>
                  <td>{{ $log->model_id }} - {{ $log->getClienteNombre() }}</td>
                  <td>{{ $log->user->name ?? 'N/A' }}</td>
                  <td>{{ $log->description }}<br><small class="text-muted">{{ $log->observacion ?? '' }}</small></td>
                  <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                  <td><span title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</span></td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center py-4">
                    <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                    <span class="text-muted">No hay registros de auditoría</span>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($logs->hasPages())
          <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 bg-light">
            <div class="text-muted small">
              Mostrando
              <b>{{ $logs->firstItem() ?? 0 }}</b>
              a
              <b>{{ $logs->lastItem() ?? 0 }}</b>
              de
              <b>{{ $logs->total() }}</b>
              registros de auditoría
            </div>
            <div>
              {{ $logs->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Tabla de Clientes -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bx bx-group me-1"></i> Lista de Clientes
            </h5>
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">
                Mostrando {{ $clientes->firstItem() ?? 0 }} a {{ $clientes->lastItem() ?? 0 }} 
                de {{ $clientes->total() }} registros
              </span>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Cliente</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Creado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse($clientes as $cliente)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                      <div class="avatar-initial rounded-circle bg-label-primary">
                        {{ substr($cliente->nombre, 0, 1) }}
                      </div>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ $cliente->nombre }}</h6>
                      <small class="text-muted">ID: {{ $cliente->id }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="fw-semibold">{{ $cliente->email }}</span>
                </td>
                <td>{{ $cliente->telefono }}</td>
                <td>
                  @if($cliente->deleted_at)
                    <span class="badge bg-label-danger">
                      <i class="bx bx-trash me-1"></i> Eliminado
                    </span>
                  @elseif($cliente->estado == 'inactivo')
                    <span class="badge bg-label-secondary">
                      <i class="bx bx-user-x me-1"></i> Inactivo
                    </span>
                  @else
                    <span class="badge bg-label-success">
                      <i class="bx bx-user-check me-1"></i> Activo
                    </span>
                  @endif
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ $cliente->created_at ? $cliente->created_at->format('d/m/Y') : '-' }}</span>
                    <small class="text-muted">{{ $cliente->created_at ? $cliente->created_at->format('H:i') : '' }}</small>
                  </div>
                </td>
                <td class="text-end">
                  <div class="dropdown" data-bs-display="static" data-bs-container="body">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                      <i class="bx bx-cog"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a class="dropdown-item" href="{{ route('clientes.show', $cliente) }}">
                          <i class="bx bx-show me-2"></i> Ver Detalles
                        </a>
                      </li>
                      @if(!$cliente->deleted_at)
                        <li>
                          <a class="dropdown-item" href="{{ route('clientes.edit', $cliente) }}">
                            <i class="bx bx-edit me-2"></i> Editar
                          </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <button class="dropdown-item text-danger" type="button" 
                                  data-bs-toggle="modal" data-bs-target="#modalEliminarCliente{{ $cliente->id }}">
                            <i class="bx bx-trash me-2"></i> Eliminar
                          </button>
                        </li>
                      @else
                        <li>
                          <button class="dropdown-item text-success" type="button" 
                                  data-bs-toggle="modal" data-bs-target="#modalRestaurarCliente{{ $cliente->id }}">
                            <i class="bx bx-refresh me-2"></i> Restaurar
                          </button>
                        </li>
                        <li>
                          <button class="dropdown-item text-danger" type="button" 
                                  data-bs-toggle="modal" data-bs-target="#modalEliminarDefinitivoCliente{{ $cliente->id }}">
                            <i class="bx bx-x-circle me-2"></i> Eliminar Definitivamente
                          </button>
                        </li>
                      @endif
                    </ul>
                  </div>
                </td>
              </tr>
              <!-- Modales para acciones -->
              @include('clientes.partials.modals', ['cliente' => $cliente])
              @empty
              <tr>
                <td colspan="6" class="text-center py-5">
                  <div class="d-flex flex-column align-items-center">
                    <i class="bx bx-user-x bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">No hay clientes {{ request('eliminados') ? 'eliminados' : 'registrados' }}</h5>
                    <p class="text-muted mb-0">No se encontraron clientes con los filtros aplicados</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <!-- Paginación -->
        @if($clientes->hasPages())
        <div class="card-footer">
          <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
              Mostrando <b>{{ $clientes->firstItem() ?? 0 }}</b> a <b>{{ $clientes->lastItem() ?? 0 }}</b> 
              de <b>{{ $clientes->total() }}</b> registros
            </div>
            <div>
              {{ $clientes->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>

<!-- Sistema de Notificaciones -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- Alerta de errores de validación (solo una, arriba) -->
@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show shadow mb-4" role="alert">
    <strong>Se encontraron los siguientes errores:</strong>
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
@endif

@endsection

@push('scripts')
<script>
// Sistema de notificaciones elegante
class NotificationSystem {
  constructor() {
    this.container = document.getElementById('notification-container');
  }

  show(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
    notification.style.cssText = `
      min-width: 300px;
      max-width: 400px;
      margin-bottom: 10px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      animation: slideInRight 0.3s ease-out;
    `;

    const iconMap = {
      success: 'bx-check-circle',
      error: 'bx-error-circle',
      warning: 'bx-warning',
      info: 'bx-info-circle'
    };

    notification.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="bx ${iconMap[type]} fs-4 me-2"></i>
        <div class="flex-grow-1">
          <div class="fw-semibold">${this.getTitle(type)}</div>
          <div class="small">${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;

    this.container.appendChild(notification);

    // Auto-remove después del tiempo especificado
    setTimeout(() => {
      this.hide(notification);
    }, duration);

    // Event listener para cerrar manualmente
    notification.querySelector('.btn-close').addEventListener('click', () => {
      this.hide(notification);
    });
  }

  hide(notification) {
    notification.style.animation = 'slideOutRight 0.3s ease-in';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }

  getTitle(type) {
    const titles = {
      success: '¡Éxito!',
      error: 'Error',
      warning: 'Advertencia',
      info: 'Información'
    };
    return titles[type] || 'Notificación';
  }
}

// Inicializar sistema de notificaciones
const notifications = new NotificationSystem();

// Mostrar notificaciones de sesión si existen
@if(session('success'))
  notifications.show('{{ session('success') }}', 'success');
@endif

@if(session('error'))
  notifications.show('{{ session('error') }}', 'error');
@endif

@if(session('warning'))
  notifications.show('{{ session('warning') }}', 'warning');
@endif

@if(session('info'))
  notifications.show('{{ session('info') }}', 'info');
@endif

// Funcionalidades adicionales
// Auto-submit de filtros
const filterInputs = document.querySelectorAll('select[name="estado"], select[name="per_page"]');
filterInputs.forEach(input => {
  input.addEventListener('change', function() {
    this.closest('form').submit();
  });
});

// Búsqueda en tiempo real
const searchInput = document.querySelector('input[name="buscar"]');
if (searchInput) {
  let searchTimeout;
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      this.closest('form').submit();
    }, 500);
  });
}

// Tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>
@endpush

@push('styles')
<style>
@keyframes slideInRight {
    from {
    transform: translateX(100%);
        opacity: 0;
    }
    to {
    transform: translateX(0);
        opacity: 1;
  }
}

@keyframes slideOutRight {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}

.table th {
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
    font-weight: 600;
  color: #495057;
}

.table td {
  vertical-align: middle;
  border-bottom: 1px solid #f1f3f4;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

.badge {
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
}

.dropdown-menu {
  border: none;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

.modal-content {
  border: none;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.modal-header {
  border-bottom: 1px solid #f1f3f4;
  padding: 1.5rem;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  border-top: 1px solid #f1f3f4;
  padding: 1.5rem;
}

.card {
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  border-radius: 12px;
}

.card-header {
  background-color: #fff;
  border-bottom: 1px solid #f1f3f4;
  padding: 1.5rem;
}

.card-body {
  padding: 1.5rem;
}

.btn-outline-primary.active {
  background-color: #696cff;
  border-color: #696cff;
  color: white;
}

@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.875rem;
  }
  
  .dropdown-menu {
    position: static !important;
    transform: none !important;
    width: 100%;
    margin-top: 0.5rem;
  }
  
  .btn-group {
    flex-direction: column;
  }
  
  .btn-group .btn {
    border-radius: 0.375rem !important;
    margin-bottom: 0.25rem;
  }
}

.dropdown-menu {
  z-index: 2050 !important;
}

@media (max-width: 768px) {
  .dropdown-menu {
    position: static !important;
    transform: none !important;
    width: 100%;
    margin-top: 0.5rem;
    z-index: 2050 !important;
  }
}
</style>
@endpush
