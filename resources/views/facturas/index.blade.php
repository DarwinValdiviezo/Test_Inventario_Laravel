@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
                <div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
                    <div class="row">
                        <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema /</span> Facturas
          </h4>
          <p class="text-muted mb-0">Gestión completa de facturación electrónica</p>
                                            </div>
        <div class="page-title-actions ms-auto">
                                                @if(!request('eliminadas'))
            <a href="{{ route('facturas.create') }}" class="btn btn-primary">
                                                    <i class="bx bx-plus me-1"></i> Nueva Factura
                                                </a>
                                                @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Estadísticas de Facturas -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Total de Facturas</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-primary mb-0 me-2">{{ $estadisticas['total'] ?? $facturas->total() }}</h4>
                <p class="mb-0">facturas</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-primary rounded p-2">
                <i class="bx bx-receipt bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Facturas Activas</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-success mb-0 me-2">{{ $estadisticas['activas'] ?? '-' }}</h4>
                <p class="mb-0">activas</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-success rounded p-2">
                <i class="bx bx-check-circle bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Facturas Anuladas</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-danger mb-0 me-2">{{ $estadisticas['anuladas'] ?? '-' }}</h4>
                <p class="mb-0">anuladas</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-danger rounded p-2">
                <i class="bx bx-x-circle bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Monto Total</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-info mb-0 me-2">${{ $estadisticas['monto_total'] ?? '-' }}</h4>
                <p class="mb-0">USD</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-info rounded p-2">
                <i class="bx bx-dollar bx-sm"></i>
              </span>
                                            </div>
                                        </div>
                </div>
            </div>
        </div>
    </div>

  {{-- Tabs para alternar entre activas y anuladas --}}
  <div class="mb-4 d-flex align-items-center gap-2">
    <a href="{{ route('facturas.index') }}" class="btn btn-outline-primary @if(!request('eliminadas')) active @endif">
      <i class="bx bx-list-ul me-1"></i> Activas
    </a>
    <a href="{{ route('facturas.index', ['eliminadas' => 1]) }}" class="btn btn-outline-danger @if(request('eliminadas')) active @endif">
      <i class="bx bx-archive me-1"></i> Anuladas
    </a>
</div>

  <!-- Filtros de Búsqueda -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
          <h5 class="card-title mb-0">
                                        <i class="bx bx-filter-alt me-2"></i> Filtros de Búsqueda
          </h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('facturas.index') }}" class="row g-3 align-items-end flex-wrap">
                <div class="col-md-3 col-12">
                                            <label class="form-label">Buscar</label>
              <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="ID factura, cliente...">
                </div>
                <div class="col-md-2 col-6">
                                            <label class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                  <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6">
                                            <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                        <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anuladas</option>
                    </select>
                </div>
                <div class="col-md-2 col-6">
                                            <label class="form-label">Mostrar</label>
                    <select name="per_page" class="form-select">
                        @foreach([5,10,15,20] as $n)
                        <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary w-100" type="submit">
                                                    <i class="bx bx-search me-1"></i> Buscar
                    </button>
                    <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary w-100">
                                                    <i class="bx bx-x me-1"></i> Limpiar
                    </a>
                                            </div>
                </div>
            </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Facturas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bx bx-receipt me-1"></i> Lista de Facturas {{ request('eliminadas') ? 'Anuladas' : 'Activas' }}
            </h5>
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">
                Mostrando {{ $facturas->firstItem() ?? 0 }} a {{ $facturas->lastItem() ?? 0 }} de {{ $facturas->total() }} registros
              </span>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="bx bx-download me-1"></i> Exportar
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#"><i class="bx bx-file me-2"></i> PDF</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bx bx-spreadsheet me-2"></i> Excel</a></li>
                                            </ul>
                                        </div>
                                    </div>
        </div>
        </div>
            <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
                                                <tr>
                                                    <th>#</th> <!-- Nueva columna para numeración -->
                                                    <th># Factura</th>
                                                    <th>Cliente</th>
                                                    <th>Total</th>
                                                    <th>Estado</th>
                                                    <th>Vendedor</th>
                                                    <th>Fecha</th>
                <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
            <tbody class="table-border-bottom-0">
                        @forelse($facturas as $i => $factura)
                                                <tr>
                                                    <td>{{ $facturas->firstItem() + $i }}</td> <!-- Numeración consecutiva -->
                                                    <td>
                                                        <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <div class="avatar-initial rounded-circle bg-label-primary">
                                                                <i class="bx bx-receipt"></i>
                        </div>
                      </div>
                      <div>
                        <h6 class="mb-0">#{{ $factura->id }}</h6>
                        <small class="text-muted">Factura Electrónica</small>
                                                            </div>
                                                        </div>
                            </td>
                            <td>
                                                        <div>
                      <span class="fw-semibold">{{ $factura->cliente->nombre ?? 'Cliente eliminado' }}</span>
                      <div class="text-muted small">{{ $factura->cliente->email ?? '' }}</div>
                                </div>
                            </td>
                            <td>
                    <span class="fw-semibold text-success">${{ number_format($factura->total, 2) }}</span>
                    <div class="text-muted small">{{ $factura->detalles->count() }} productos</div>
                            </td>
                            <td>
                                @if($factura->estado === 'activa')
                      <span class="badge bg-label-success">Activa</span>
                                @elseif($factura->estado === 'anulada')
                      <span class="badge bg-label-danger">Anulada</span>
                                @else
                      <span class="badge bg-label-secondary">{{ ucfirst($factura->estado) }}</span>
                                @endif
                            </td>
                            <td>
                                                        <div>
                      <span class="fw-semibold">{{ $factura->usuario->name ?? 'Usuario eliminado' }}</span>
                      <div class="text-muted small">{{ $factura->usuario->email ?? '' }}</div>
                                                            @if($factura->usuario_id === auth()->id())
                        <small class="badge bg-label-primary">Tú emitiste</small>
                                                            @elseif(auth()->user()->hasRole('Administrador'))
                        <small class="badge bg-label-info">Administrador</small>
                                                            @else
                        <small class="badge bg-label-secondary">Otro usuario</small>
                                                            @endif
                                </div>
                            </td>
                            <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">{{ $factura->created_at->format('d/m/Y') }}</span>
                      <small class="text-muted">{{ $factura->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                  <td class="text-end">
                    <div class="dropdown" data-bs-display="static" data-bs-container="body">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bx bx-cog"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="{{ route('facturas.show', $factura) }}">
                            <i class="bx bx-show me-2"></i> Ver Detalles
                          </a>
                        </li>
                                    @if(!request('eliminadas'))
                                                            @can('update', $factura)
                            <li>
                              <a class="dropdown-item" href="{{ route('facturas.edit', $factura->id) }}">
                                <i class="bx bx-edit me-2"></i> Editar
                                                            </a>
                            </li>
                                                            @endcan
                                                            @can('delete', $factura)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                              <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalAnularFactura{{ $factura->id }}">
                                <i class="bx bx-x-circle me-2"></i> Anular
                                    </button>
                            </li>
                                                            @endcan
                                    @else
                                                            @can('restore', $factura)
                            <li>
                              <button class="dropdown-item text-success" type="button" data-bs-toggle="modal" data-bs-target="#modalRestaurarFactura{{ $factura->id }}">
                                <i class="bx bx-refresh me-2"></i> Restaurar
                                    </button>
                            </li>
                                                            @endcan
                                                            @can('forceDelete', $factura)
                            <li>
                              <button class="dropdown-item text-dark" type="button" data-bs-toggle="modal" data-bs-target="#modalBorrarDefinitivoFactura{{ $factura->id }}">
                                <i class="bx bx-trash me-2"></i> Borrar Definitivo
                                    </button>
                            </li>
                                                            @endcan
                                    @endif
                      </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                      <h5 class="text-muted">No hay facturas {{ request('eliminadas') ? 'anuladas' : 'registradas' }}</h5>
                      <p class="text-muted mb-0">{{ request('eliminadas') ? 'No se encontraron facturas anuladas' : 'Comienza creando una nueva factura' }}</p>
                                                            @if(!request('eliminadas'))
                        <a href="{{ route('facturas.create') }}" class="btn btn-primary mt-3">
                                                                    <i class="bx bx-plus me-1"></i> Crear Factura
                                                                </a>
                                                            @endif
                                                        </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>
                                <div class="card-footer d-flex align-items-center flex-wrap gap-2">
                                    <p class="m-0 text-muted flex-grow-1">
                                        Mostrando <span>{{ $facturas->firstItem() ?? 0 }}</span> a <span>{{ $facturas->lastItem() ?? 0 }}</span> de <span>{{ $facturas->total() }}</span> registros
                                    </p>
                                    <ul class="pagination m-0 ms-auto">
                                        {{ $facturas->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                                    </ul>
            </div>
            </div>
        </div>
    </div>

  @if(request('eliminadas'))
    {{-- Tabla de auditoría de facturas anuladas --}}
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between bg-light">
        <span><i class="bx bx-history me-2"></i> Auditoría de Facturas Anuladas</span>
                                </div>
      <div class="card-body p-0">
            <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                        <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                            <th>Acción</th>
                <th>Modelo</th>
                <th>Afectado</th>
                <th>Descripción</th>
                <th>IP</th>
                <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                                                    <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">{{ $log->created_at->format('d/m/Y') }}</span>
                      <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                    </div>
                  </td>
                  <td>
                    @if($log->user)
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                          <div class="avatar-initial rounded-circle bg-label-primary">
                            {{ substr($log->user->name, 0, 1) }}
                          </div>
                        </div>
                        <div>
                          <span class="fw-semibold">{{ $log->user->name }}</span>
                          <small class="text-muted d-block">{{ $log->user->email }}</small>
                        </div>
                      </div>
                    @else
                      <span class="text-danger">Desconocido</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $actionColors = [
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        'restore' => 'info',
                        'forceDelete' => 'dark',
                      ];
                      $color = $actionColors[$log->action] ?? 'primary';
                    @endphp
                    <span class="badge bg-label-{{ $color }}">{{ ucfirst($log->action) }}</span>
                                                    </td>
                                                    <td>
                    <span class="badge bg-label-info">{{ class_basename($log->model_type) }}</span>
                                                    </td>
                                                    <td>
                    <span class="fw-semibold">{{ $log->getAfectado() ?? '-' }}</span>
                                                    </td>
                                                    <td>
                    <span class="text-truncate d-inline-block" style="max-width: 200px;">
                      {{ $log->descripcion ?? $log->observacion ?? '-' }}
                    </span>
                                                    </td>
                                                    <td>
                    @if($log->ip_address)
                      <small class="text-muted">{{ $log->ip_address }}</small>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                                                    </td>
                                                    <td>
                    @if($log->old_values || $log->new_values)
                      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $log->id }}">
                        <i class="bx bx-show"></i>
                      </button>
                      {{-- Modal de detalles --}}
                      <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $log->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="detailsModalLabel{{ $log->id }}">Detalles de Auditoría</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-md-6">
                                  <h6>Valores Anteriores</h6>
                                  <pre class="bg-light p-2 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                <div class="col-md-6">
                                  <h6>Valores Nuevos</h6>
                                  <pre class="bg-light p-2 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                                                    </td>
                        </tr>
                        @empty
                        <tr>
                  <td colspan="8" class="text-center text-muted">No hay registros de auditoría para facturas anuladas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        @endif
    </div>

<!-- Sistema de Notificaciones -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- Modales para acciones -->
@foreach($facturas as $factura)
@if(!request('eliminadas'))
<!-- Modal Anular Factura -->
    <div class="modal fade" id="modalAnularFactura{{ $factura->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
              <i class="bx bx-x-circle text-danger me-2"></i> Anular Factura
                </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
          <form method="POST" action="{{ route('facturas.destroy', $factura) }}" id="formAnularFactura{{ $factura->id }}">
        @csrf
        @method('DELETE')
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
  @else
    {{-- Modal restaurar --}}
    <div class="modal fade" id="modalRestaurarFactura{{ $factura->id }}" tabindex="-1" aria-labelledby="modalRestaurarFacturaLabel{{ $factura->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalRestaurarFacturaLabel{{ $factura->id }}">¿Restaurar Factura?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
      <form method="POST" action="{{ route('facturas.restore', $factura->id) }}">
        @csrf
        <!-- IMPORTANTE: No usar @method('PUT') ni ningún otro método, solo POST -->
        <div class="modal-body">
              <div class="mb-3 p-2 bg-light rounded border">
                <strong>ID:</strong> #{{ $factura->id }}<br>
                <strong>Cliente:</strong> {{ $factura->cliente->nombre ?? '-' }}<br>
                <strong>Total:</strong> ${{ number_format($factura->total, 2) }}<br>
                <strong>Fecha:</strong> {{ $factura->fecha_emision->format('d/m/Y') }}
              </div>
                  <div class="mb-3">
                <label class="form-label">Contraseña de Administrador</label>
                                <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
                  </div>
                  <div class="mb-3">
                <label class="form-label">Motivo de Restauración</label>
            <select name="observacion" class="form-select" required>
                                    <option value="">Seleccione un motivo</option>
              <option value="Error en la anulación">Error en la anulación</option>
              <option value="Solicitud del cliente">Solicitud del cliente</option>
              <option value="Corrección de datos">Corrección de datos</option>
              <option value="Producto disponible nuevamente">Producto disponible nuevamente</option>
              <option value="Problema de pago resuelto">Problema de pago resuelto</option>
              <option value="Otro">Otro</option>
            </select>
                            </div>
                            <div class="alert alert-success">
                <i class="bx bx-info-circle me-2"></i>
                <strong>¡Información!</strong> La factura volverá a estar activa en el sistema.
                            </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="confirm_delete" id="confirmRestaurarFactura{{ $factura->id }}" required>
                <label class="form-check-label" for="confirmRestaurarFactura{{ $factura->id }}">
                  Confirmo que deseo restaurar esta factura
                </label>
          </div>
        </div>
        <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success">Restaurar</button>
        </div>
      </form>
    </div>
  </div>
</div>
    {{-- Modal borrar definitivo --}}
    <div class="modal fade" id="modalBorrarDefinitivoFactura{{ $factura->id }}" tabindex="-1" aria-labelledby="modalBorrarDefinitivoFacturaLabel{{ $factura->id }}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="modalBorrarDefinitivoFacturaLabel{{ $factura->id }}">¿Borrar Factura Definitivamente?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
      <form method="POST" action="{{ route('facturas.force-delete', $factura->id) }}">
        @csrf
        <!-- IMPORTANTE: No usar @method('DELETE'), solo POST -->
        <div class="modal-body">
              <div class="alert alert-warning mb-3">
                <strong>¡Atención!</strong> Esta acción <strong>no se puede deshacer</strong>.
              </div>
              <div class="mb-3 p-2 bg-light rounded border">
                <strong>ID:</strong> #{{ $factura->id }}<br>
                <strong>Cliente:</strong> {{ $factura->cliente->nombre ?? '-' }}<br>
                <strong>Total:</strong> ${{ number_format($factura->total, 2) }}<br>
                <strong>Fecha:</strong> {{ $factura->fecha_emision->format('d/m/Y') }}
              </div>
                  <div class="mb-3">
                <label class="form-label">Contraseña de Administrador</label>
                                <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
                  </div>
                  <div class="mb-3">
                <label class="form-label">Motivo del Borrado Definitivo</label>
            <select name="observacion" class="form-select" required>
                                    <option value="">Seleccione un motivo</option>
              <option value="Factura obsoleta">Factura obsoleta</option>
              <option value="Datos incorrectos">Datos incorrectos</option>
              <option value="Duplicado en el sistema">Duplicado en el sistema</option>
              <option value="Problemas de seguridad">Problemas de seguridad</option>
              <option value="Limpieza de base de datos">Limpieza de base de datos</option>
              <option value="Error en el sistema">Error en el sistema</option>
              <option value="Otro">Otro</option>
            </select>
                            </div>
                            <div class="alert alert-dark">
                <i class="bx bx-error-circle me-2"></i>
                <strong>¡ACCIÓN CRÍTICA E IRREVERSIBLE!</strong> Esta acción eliminará <strong>PERMANENTEMENTE</strong> todos los datos de la factura. No podrás recuperarlos bajo ninguna circunstancia.
                            </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="confirm_delete" id="confirmBorrarDefinitivoFactura{{ $factura->id }}" required>
                <label class="form-check-label" for="confirmBorrarDefinitivoFactura{{ $factura->id }}">
                  Confirmo que deseo borrar esta factura permanentemente
                </label>
          </div>
        </div>
        <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-outline-danger">Borrar definitivo</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Para cada modal de anulación de factura
  document.querySelectorAll('[id^="modalAnularFactura"]').forEach(function(modal) {
    const form = modal.querySelector('form');
    if (!form) return;
    const passwordInput = form.querySelector('input[name="password"]');
    const motivoSelect = form.querySelector('select[name="observacion"]');
    const confirmCheck = form.querySelector('input[name="confirm_delete"]');
    const submitBtn = form.querySelector('button[type="submit"]');
    function checkFields() {
      const passOk = passwordInput && passwordInput.value.length > 0;
      const motivoOk = motivoSelect && motivoSelect.value.length > 0;
      const confirmOk = confirmCheck && confirmCheck.checked;
      submitBtn.disabled = !(passOk && motivoOk && confirmOk);
    }
    if (passwordInput) passwordInput.addEventListener('input', checkFields);
    if (motivoSelect) motivoSelect.addEventListener('change', checkFields);
    if (confirmCheck) confirmCheck.addEventListener('change', checkFields);
    // Inicializar estado
    checkFields();
  });
});
</script>
@if(session('success'))
  <script>window.facturasManager?.showNotification(@json(session('success')), 'success');</script>
@endif
@if(session('error'))
  <script>window.facturasManager?.showNotification(@json(session('error')), 'error');</script>
@endif
@if(session('warning'))
  <script>window.facturasManager?.showNotification(@json(session('warning')), 'warning');</script>
@endif
@if(session('info'))
  <script>window.facturasManager?.showNotification(@json(session('info')), 'info');</script>
@endif
@endpush

@push('styles')
<style>
@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOutRight {
  from { transform: translateX(0); opacity: 1; }
  to { transform: translateX(100%); opacity: 0; }
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
  z-index: 2050 !important;
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
.card-info p {
  color: #6c757d;
  font-size: 0.875rem;
    margin-bottom: 0.5rem;
}
.card-icon {
  display: flex;
  align-items: center;
}
@media (max-width: 768px) {
  .table-responsive { font-size: 0.875rem; }
  .dropdown-menu {
    position: static !important;
    transform: none !important;
        width: 100%;
    margin-top: 0.5rem;
    z-index: 2050 !important;
  }
  .btn-group { flex-direction: column; }
  .btn-group .btn {
    border-radius: 0.375rem !important;
    margin-bottom: 0.25rem;
    }
}
</style>
@endpush 