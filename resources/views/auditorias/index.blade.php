@extends('layouts.app')

@section('title', 'Auditoría del Sistema')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema /</span> Auditoría
          </h4>
          <p class="text-muted mb-0">Registro de actividades y cambios en el sistema</p>
        </div>
        <div class="page-title-actions ms-auto">
          <a href="{{ route('auditorias.export') }}?{{ http_build_query(request()->all()) }}" 
             class="btn btn-primary">
            <i class="bx bx-download me-1"></i> Exportar CSV
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Estadísticas -->
  <div class="row g-4 mb-4">
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Total Registros</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ number_format($stats['total_logs']) }}</h4>
                <small class="text-success">+{{ $stats['this_month_logs'] }} este mes</small>
              </div>
            </div>
            <span class="badge bg-label-primary rounded p-2">
              <i class="bx bx-history bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Hoy</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ number_format($stats['today_logs']) }}</h4>
                <small class="text-success">+{{ $stats['today_logs'] }} actividades</small>
              </div>
            </div>
            <span class="badge bg-label-success rounded p-2">
              <i class="bx bx-calendar-check bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Usuarios Activos</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ number_format($stats['unique_users']) }}</h4>
                <small class="text-success">usuarios únicos</small>
              </div>
            </div>
            <span class="badge bg-label-info rounded p-2">
              <i class="bx bx-user-check bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="fw-semibold d-block mb-1">Creaciones</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ number_format($stats['actions_count']['create']) }}</h4>
                <small class="text-success">nuevos registros</small>
              </div>
            </div>
            <span class="badge bg-label-warning rounded p-2">
              <i class="bx bx-plus-circle bx-sm"></i>
            </span>
          </div>
        </div>
        </div>
    </div>
  </div>

  <!-- Filtros y Tabla -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <div class="card-title mb-0">
            <h5 class="mb-0">Registro de Auditoría</h5>
            <small class="text-muted">Filtra y visualiza todas las actividades del sistema</small>
          </div>
        </div>
        
        <!-- Filtros -->
        <div class="card-body border-bottom">
          <form method="GET" action="{{ route('auditorias.index') }}" class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Usuario</label>
              <select name="user_id" class="form-select">
                <option value="">Todos los usuarios</option>
                @foreach($users as $user)
                  <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                  </option>
                @endforeach
              </select>
                </div>
            <div class="col-md-2">
              <label class="form-label">Acción</label>
              <select name="action" class="form-select">
                <option value="">Todas las acciones</option>
                @foreach($actions as $action)
                  <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                    {{ ucfirst($action) }}
                  </option>
                @endforeach
              </select>
                </div>
            <div class="col-md-2">
              <label class="form-label">Modelo</label>
              <select name="model_type" class="form-select">
                <option value="">Todos los modelos</option>
                @foreach($modelTypes as $modelType)
                  <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                    {{ class_basename($modelType) }}
                  </option>
                @endforeach
              </select>
                </div>
            <div class="col-md-2">
              <label class="form-label">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control">
                </div>
            <div class="col-md-2">
              <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control">
                </div>
            <div class="col-md-1">
              <label class="form-label">&nbsp;</label>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bx bx-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla -->
            <div class="table-responsive">
          <table class="table table-hover">
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
            <tbody class="table-border-bottom-0">
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
                        'delete' => 'danger'
                      ];
                      $color = $actionColors[$log->action] ?? 'primary';
                    @endphp
                    <span class="badge bg-label-{{ $color }}">{{ ucfirst($log->action) }}</span>
                  </td>
                  <td>
                    <span class="badge bg-label-info">{{ class_basename($log->model_type) }}</span>
                  </td>
                  <td>
                    <span class="fw-semibold">{{ $log->getAfectado() }}</span>
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
                      <button type="button" class="btn btn-sm btn-outline-primary" 
                              data-bs-toggle="modal" 
                              data-bs-target="#detailsModal{{ $log->id }}">
                        <i class="bx bx-show"></i>
                      </button>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                      <i class="bx bx-history bx-lg text-muted mb-2"></i>
                      <span class="text-muted">No hay registros de auditoría</span>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        @if($logs->hasPages())
          <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted">
                Mostrando <b>{{ $logs->firstItem() ?? 0 }}</b> a <b>{{ $logs->lastItem() ?? 0 }}</b> 
                de <b>{{ $logs->total() }}</b> registros
              </div>
              <div>
                {{ $logs->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Reportes y Estadísticas -->
  <div class="row g-4 mt-4">
    <!-- Actividad Reciente -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-time me-1"></i> Actividad Reciente
          </h5>
        </div>
        <div class="card-body">
          <div class="timeline">
            @foreach($recentActivity->take(8) as $activity)
              <div class="timeline-item">
                <div class="timeline-marker bg-label-{{ $actionColors[$activity->action] ?? 'primary' }}"></div>
                <div class="timeline-content">
                  <h6 class="mb-1">{{ $activity->user ? $activity->user->name : 'Desconocido' }}</h6>
                  <p class="mb-1">{{ ucfirst($activity->action) }} {{ class_basename($activity->model_type) }}</p>
                  <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <!-- Estadísticas por Usuario -->
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-user me-1"></i> Actividad por Usuario
          </h5>
        </div>
        <div class="card-body">
          @foreach($activityByUser as $userActivity)
            @php $user = $userActivity->user @endphp
            @if($user)
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-3">
                    <div class="avatar-initial rounded-circle bg-label-primary">
                      {{ substr($user->name, 0, 1) }}
                    </div>
                  </div>
                  <div>
                    <h6 class="mb-0">{{ $user->name }}</h6>
                    <small class="text-muted">{{ $user->email }}</small>
                  </div>
                </div>
                <div class="text-end">
                  <span class="badge bg-label-primary">{{ $userActivity->total }}</span>
                  <small class="text-muted d-block">actividades</small>
                </div>
              </div>
            @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <!-- Gráficos de Estadísticas -->
  <div class="row g-4 mt-4">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-bar-chart me-1"></i> Actividad por Acción
          </h5>
        </div>
        <div class="card-body">
          <div id="actionsChart" style="height: 300px;"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-pie-chart me-1"></i> Actividad por Modelo
          </h5>
        </div>
        <div class="card-body">
          <div id="modelsChart" style="height: 300px;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modales para detalles -->
@foreach($logs as $log)
                                @if($log->old_values || $log->new_values)
    <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalles de Cambios</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Campo</th>
                    <th>Valor Anterior</th>
                    <th>Valor Nuevo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $old = $log->old_values ? json_decode($log->old_values, true) : [];
                                                        $new = $log->new_values ? json_decode($log->new_values, true) : [];
                                                        $allKeys = collect(array_merge(array_keys($old), array_keys($new)))->unique();
                                                    @endphp
                                                    @foreach($allKeys as $key)
                                                        <tr>
                      <td class="fw-semibold">{{ $key }}</td>
                                                            <td class="text-danger">
                                                                @if(isset($old[$key]))
                                                                    @if(is_array($old[$key]))
                                                                        <pre class="mb-0 small bg-light border rounded p-1">{{ json_encode($old[$key], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                    @else
                                                                        {{ $old[$key] }}
                                                                    @endif
                                                                @else
                          <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-success">
                                                                @if(isset($new[$key]))
                                                                    @if(is_array($new[$key]))
                                                                        <pre class="mb-0 small bg-light border rounded p-1">{{ json_encode($new[$key], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                    @else
                                                                        {{ $new[$key] }}
                                                                    @endif
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
    </div>
</div>
  @endif
@endforeach
@endsection 

@push('scripts')
<script src="/sneat/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Gráfico de acciones
  const actionsData = @json($activityByAction);
  const actionsChart = new ApexCharts(document.querySelector("#actionsChart"), {
    chart: {
      type: 'bar',
      height: 300,
      toolbar: { show: false }
    },
    series: [{
      name: 'Actividades',
      data: actionsData.map(item => item.total)
    }],
    xaxis: {
      categories: actionsData.map(item => item.action.charAt(0).toUpperCase() + item.action.slice(1))
    },
    colors: ['#696cff'],
    plotOptions: {
      bar: {
        borderRadius: 4,
        horizontal: false,
      }
    }
  });
  actionsChart.render();

  // Gráfico de modelos
  const modelsData = @json($activityByModel);
  const modelsChart = new ApexCharts(document.querySelector("#modelsChart"), {
    chart: {
      type: 'pie',
      height: 300
    },
    series: modelsData.map(item => item.total),
    labels: modelsData.map(item => item.model_type.split('\\').pop()),
    colors: ['#696cff', '#8592a3', '#71dd37', '#ff3e1d', '#03c3ec'],
    legend: {
      position: 'bottom'
    }
  });
  modelsChart.render();
});
</script>
@endpush

@push('styles')
<style>
.timeline {
  position: relative;
  padding-left: 30px;
}

.timeline-item {
  position: relative;
  margin-bottom: 20px;
}

.timeline-marker {
  position: absolute;
  left: -35px;
  top: 0;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 3px #e7e7ff;
}

.timeline-content {
  padding-left: 15px;
}

.timeline-item:not(:last-child)::after {
  content: '';
  position: absolute;
  left: -29px;
  top: 12px;
  width: 2px;
  height: calc(100% + 8px);
  background: #e7e7ff;
}
</style>
@endpush 