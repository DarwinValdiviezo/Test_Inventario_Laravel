@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Inventario /</span> Productos
          </h4>
          <p class="text-muted mb-0">Gestión completa de productos del sistema</p>
        </div>
        <div class="page-title-actions ms-auto">
          @if(!request('eliminados'))
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
              <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs para activos/eliminados -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('productos.index') }}" class="btn btn-outline-primary {{ !request('eliminados') ? 'active' : '' }}">
              <i class="bi bi-box-seam me-1"></i> Activos
            </a>
            <a href="{{ route('productos.index', array_merge(request()->except('page'), ['eliminados' => 1])) }}" class="btn btn-outline-danger {{ request('eliminados') ? 'active' : '' }}">
              <i class="bi bi-trash me-1"></i> Eliminados
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

{{-- SOLO mostrar cards y filtros si NO es eliminados --}}
@if(!request('eliminados'))
  {{-- Cards de estadísticas mejoradas --}}
  <div class="row g-4 mb-4">
    <div class="col-xl-2 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Total Productos</span>
              <h4 class="mb-0">{{ $stats['total'] ?? ($productos->total() ?? 0) }}</h4>
            </div>
            <span class="badge bg-label-primary rounded p-2" data-bs-toggle="tooltip" title="Cantidad total de productos registrados">
              <i class="bi bi-box-seam bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Bajo Stock</span>
              <h4 class="mb-0">{{ $stats['bajo_stock'] ?? 0 }}</h4>
            </div>
            <span class="badge bg-label-warning rounded p-2" data-bs-toggle="tooltip" title="Productos con stock bajo">
              <i class="bi bi-exclamation-triangle bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Categorías</span>
              <h4 class="mb-0">{{ $stats['categorias'] ?? ($categorias->count() ?? 0) }}</h4>
            </div>
            <span class="badge bg-label-info rounded p-2" data-bs-toggle="tooltip" title="Cantidad de categorías activas">
              <i class="bi bi-tags bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Eliminados</span>
              <h4 class="mb-0">{{ $stats['eliminados'] ?? 0 }}</h4>
            </div>
            <span class="badge bg-label-danger rounded p-2" data-bs-toggle="tooltip" title="Productos eliminados (pueden restaurarse)">
              <i class="bi bi-trash bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    
   
  </div>
  {{-- Botón de ayuda --}}
  <div class="d-flex justify-content-end mb-2">
    <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalAyudaProductos">
      <i class="bi bi-question-circle me-1"></i> Ayuda / ¿Cómo funcionan los reportes?
    </button>
  </div>
  {{-- Modal de ayuda --}}
  <div class="modal fade" id="modalAyudaProductos" tabindex="-1" aria-labelledby="modalAyudaProductosLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAyudaProductosLabel">Ayuda y Reportes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <ul>
            <li>Usa los filtros para buscar productos por nombre, categoría, stock o precio.</li>
            <li>Puedes exportar la lista a CSV o PDF desde el botón "Exportar / Reportes".</li>
            <li>Haz clic en "Ver Gráficas" para visualizar diferentes tipos de gráficos de stock y ventas.</li>
            <li>La tabla de auditoría muestra todos los cambios y acciones sobre productos.</li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  {{-- Filtros avanzados --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bi bi-search me-1"></i> Filtros de Búsqueda y Avanzados
          </h5>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('productos.index') }}" class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Buscar</label>
              <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Nombre, descripción...">
            </div>
            <div class="col-md-2">
              <label class="form-label">Categoría</label>
              <select name="categoria_id" class="form-select">
                <option value="">Todas</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Precio (mín)</label>
              <input type="number" name="precio_min" value="{{ request('precio_min') }}" class="form-control" min="0" step="0.01">
            </div>
            <div class="col-md-2">
              <label class="form-label">Precio (máx)</label>
              <input type="number" name="precio_max" value="{{ request('precio_max') }}" class="form-control" min="0" step="0.01">
            </div>
            <div class="col-md-1">
              <label class="form-label">Stock (mín)</label>
              <input type="number" name="stock_min" value="{{ request('stock_min') }}" class="form-control" min="0">
            </div>

            <div class="col-md-1">
              <label class="form-label">Mostrar</label>
              <select name="per_page" class="form-select">
                @foreach([5,10,15,20,50] as $n)
                  <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-12 d-flex gap-2 mt-2">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i> Buscar
              </button>
              <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i> Limpiar
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endif

{{-- Botón de exportar/reportes y ver gráficas --}}
<div class="d-flex justify-content-end mb-3 gap-2">
  <div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
      <i class="bi bi-download me-1"></i> Exportar / Reportes
    </button>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="{{ route('productos.export', ['type' => 'csv'] + request()->all()) }}"><i class="bi bi-filetype-csv me-2"></i> Exportar CSV</a></li>
      <li><a class="dropdown-item" href="{{ route('productos.export', ['type' => 'pdf'] + request()->all()) }}"><i class="bi bi-file-earmark-pdf me-2"></i> Exportar PDF</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="{{ route('productos.reporte', request()->all()) }}"><i class="bi bi-bar-chart-line me-2"></i> Reporte Gráfico</a></li>
    </ul>
  </div>

</div>

{{-- Tabla de productos y modales --}}
@if(!request('eliminados'))
  <!-- Tabla de Productos -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-box-seam me-1"></i> Lista de Productos
            </h5>
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">
                Mostrando {{ $productos->firstItem() ?? 0 }} a {{ $productos->lastItem() ?? 0 }} de {{ $productos->total() }} registros
              </span>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse($productos as $producto)
                <tr>
                  <td>
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('img/default-150x150.png') }}" class="avatar-initial rounded-circle bg-label-primary" style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                  </td>
                  <td class="fw-bold">
                    <a href="{{ route('productos.show', $producto) }}" class="text-decoration-none text-dark fw-semibold">
                      <i class="bi bi-cube text-primary"></i> {{ $producto->nombre }}
                    </a>
                  </td>
                  <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                  <td>{{ $producto->stock }}</td>
                  <td>${{ number_format($producto->precio, 2) }}</td>
                  <td>
                    @if($producto->deleted_at)
                      <span class="badge bg-label-danger" data-bs-toggle="tooltip" title="Producto eliminado">
                        <i class="bi bi-trash me-1"></i> Eliminado
                      </span>
                    @else
                      <span class="badge bg-label-success" data-bs-toggle="tooltip" title="Producto activo">
                        <i class="bi bi-check-circle me-1"></i> Activo
                      </span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="dropdown" data-bs-display="static" data-bs-container="body">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="{{ route('productos.show', $producto) }}">
                            <i class="bi bi-eye me-2"></i> Ver Detalles
                          </a>
                        </li>
                        @if(!$producto->deleted_at)
                          <li>
                            <a class="dropdown-item" href="{{ route('productos.edit', $producto) }}">
                              <i class="bi bi-pencil me-2"></i> Editar
                            </a>
                          </li>
                          <li><hr class="dropdown-divider"></li>
                          <li>
                            <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalEliminarProducto{{ $producto->id }}">
                              <i class="bi bi-trash me-2"></i> Eliminar
                            </button>
                          </li>
                        @else
                          <li>
                            <button class="dropdown-item text-success" type="button" data-bs-toggle="modal" data-bs-target="#modalRestaurarProducto{{ $producto->id }}">
                              <i class="bi bi-arrow-clockwise me-2"></i> Restaurar
                            </button>
                          </li>
                          <li>
                            <button class="dropdown-item text-dark" type="button" data-bs-toggle="modal" data-bs-target="#modalBorrarDefinitivoProducto{{ $producto->id }}">
                              <i class="bi bi-x-circle me-2"></i> Borrar definitivo
                            </button>
                          </li>
                        @endif
                      </ul>
                    </div>
                  </td>
                </tr>
                {{-- Modales para eliminar/restaurar/borrar definitivo --}}
                @include('productos.partials.modales', ['producto' => $producto])
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <i class="bi bi-box-x bx-lg text-muted mb-3"></i>
                      <h5 class="text-muted">No hay productos {{ request('eliminados') ? 'eliminados' : 'registrados' }}</h5>
                      <p class="text-muted mb-0">No se encontraron productos con los filtros aplicados</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <!-- Paginación -->
        @if($productos->hasPages())
          <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted">
                Mostrando <b>{{ $productos->firstItem() ?? 0 }}</b> a <b>{{ $productos->lastItem() ?? 0 }}</b> de <b>{{ $productos->total() }}</b> registros
              </div>
              <div>
                {{ $productos->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endif

{{-- SOLO mostrar tabla de productos eliminados y auditoría si es eliminados --}}
@if(request('eliminados'))
  <!-- Tabla de Productos Eliminados -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-trash me-1"></i> Productos Eliminados
            </h5>
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">
                Mostrando {{ $productos->firstItem() ?? 0 }} a {{ $productos->lastItem() ?? 0 }} de {{ $productos->total() }} registros
              </span>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse($productos as $producto)
                <tr>
                  <td>
                    <div class="avatar avatar-sm me-2">
                      <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('img/default-150x150.png') }}" class="avatar-initial rounded-circle bg-label-primary" style="width: 40px; height: 40px; object-fit: cover;">
                    </div>
                  </td>
                  <td class="fw-bold">
                    <a href="{{ route('productos.show', $producto) }}" class="text-decoration-none text-dark fw-semibold">
                      <i class="bi bi-cube text-primary"></i> {{ $producto->nombre }}
                    </a>
                  </td>
                  <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                  <td>{{ $producto->stock }}</td>
                  <td>${{ number_format($producto->precio, 2) }}</td>
                  <td>
                    <span class="badge bg-label-danger" data-bs-toggle="tooltip" title="Producto eliminado">
                      <i class="bi bi-trash me-1"></i> Eliminado
                    </span>
                  </td>
                  <td class="text-end">
                    <div class="dropdown" data-bs-display="static" data-bs-container="body">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="{{ route('productos.show', $producto) }}">
                            <i class="bi bi-eye me-2"></i> Ver Detalles
                          </a>
                        </li>
                        <li>
                          <button class="dropdown-item text-success" type="button" data-bs-toggle="modal" data-bs-target="#modalRestaurarProducto{{ $producto->id }}">
                            <i class="bi bi-arrow-clockwise me-2"></i> Restaurar
                          </button>
                        </li>
                        <li>
                          <button class="dropdown-item text-dark" type="button" data-bs-toggle="modal" data-bs-target="#modalBorrarDefinitivoProducto{{ $producto->id }}">
                            <i class="bi bi-x-circle me-2"></i> Eliminar Definitivamente
                          </button>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
                {{-- Modales para restaurar/borrar definitivo --}}
                @include('productos.partials.modales', ['producto' => $producto])
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <i class="bi bi-box-x bx-lg text-muted mb-3"></i>
                      <h5 class="text-muted">No hay productos eliminados</h5>
                      <p class="text-muted mb-0">No se encontraron productos eliminados con los filtros aplicados</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <!-- Paginación -->
        @if($productos->hasPages())
          <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted">
                Mostrando <b>{{ $productos->firstItem() ?? 0 }}</b> a <b>{{ $productos->lastItem() ?? 0 }}</b> de <b>{{ $productos->total() }}</b> registros
              </div>
              <div>
                {{ $productos->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
  {{-- Filtros de auditoría --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bi bi-search me-1"></i> Filtros de Auditoría
          </h5>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('productos.index', ['eliminados' => 1]) }}" class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Buscar en auditoría</label>
              <input type="text" name="log_buscar" value="{{ request('log_buscar') }}" class="form-control" placeholder="Descripción, observación, usuario...">
            </div>
            <div class="col-md-3">
              <label class="form-label">Acción</label>
              <select name="log_accion" class="form-select">
                <option value="">Todas</option>
                <option value="delete" {{ request('log_accion') == 'delete' ? 'selected' : '' }}>Eliminado</option>
                <option value="restore" {{ request('log_accion') == 'restore' ? 'selected' : '' }}>Restaurado</option>
                <option value="forceDelete" {{ request('log_accion') == 'forceDelete' ? 'selected' : '' }}>Eliminado Definitivo</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Usuario</label>
              <select name="log_usuario" class="form-select">
                <option value="">Todos</option>
                @foreach($usuarios as $usuario)
                  <option value="{{ $usuario->id }}" {{ request('log_usuario') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search me-1"></i> Buscar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  {{-- Tabla de reportes/auditoría de productos --}}
  @if(isset($logs) && $logs->count())
  <div class="card mt-4">
    <div class="card-header d-flex align-items-center justify-content-between bg-light">
      <span><i class="bi bi-history me-2"></i> Auditoría de Productos</span>
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
            @foreach($logs as $log)
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
                      <i class="bi bi-eye"></i>
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
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
@endif
<!-- Sistema de Notificaciones -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
@endsection

@push('scripts')
<script>
// Sistema de notificaciones elegante (igual que usuarios)
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
      success: 'bi-check-circle-fill',
      error: 'bi-exclamation-triangle-fill',
      warning: 'bi-exclamation-triangle-fill',
      info: 'bi-info-circle-fill'
    };

    notification.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="bi ${iconMap[type]} fs-4 me-2"></i>
        <div class="flex-grow-1">
          <div class="fw-semibold">${this.getTitle(type)}</div>
          <div class="small">${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;

    this.container.appendChild(notification);

    setTimeout(() => {
      this.hide(notification);
    }, duration);

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

const notifications = new NotificationSystem();

// Mostrar notificaciones de sesión si existen
@if(session('success'))
  notifications.show(`{{ session('success') }}`, 'success');
@endif
@if(session('error'))
  notifications.show(`{{ session('error') }}`, 'error');
@endif
@if(session('warning'))
  notifications.show(`{{ session('warning') }}`, 'warning');
@endif
@if(session('info'))
  notifications.show(`{{ session('info') }}`, 'info');
@endif

// Mostrar errores de validación
@if($errors->any())
  @foreach($errors->all() as $error)
    notifications.show(`{{ $error }}`, 'error');
  @endforeach
@endif
</script>
@endpush

@push('styles')
<style>
/* Toast de error rojo sólido */
.alert-danger, .toast-error, .alert.alert-danger.shadow-lg {
  background: linear-gradient(135deg, #ff3b3b 0%, #b80000 100%) !important;
  color: #fff !important;
  border: none !important;
  box-shadow: 0 4px 20px rgba(184,0,0,0.15) !important;
  opacity: 1 !important;
}
.alert-danger .btn-close, .toast-error .btn-close {
  filter: invert(1);
}
</style>
@endpush
