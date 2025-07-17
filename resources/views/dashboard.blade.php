@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Notificaciones visuales -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow mb-4" role="alert">
      <i class="bx bx-check-circle me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow mb-4" role="alert">
      <i class="bx bx-x-circle me-2"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  @endif

  <!-- Card bienvenida -->
  <div class="card mb-4 animate__animated animate__fadeInDown">
    <div class="card-body d-flex align-items-center justify-content-between">
      <div>
        <h4 class="mb-1">¡Bienvenido {{ Auth::user()->name }}! 🎉</h4>
        <p class="mb-2 text-muted">Panel general de tu sistema de inventario y facturación.</p>
      </div>
      <span class="dashboard-admin-welcome-icon">
        <i class="bx bx-crown" style="font-size: 90px; color: #bdbdbd;"></i>
      </span>
    </div>
    <span class="badge bg-label-primary position-absolute top-0 end-0 m-3">{{ Auth::user()->getRoleNames()->first() }}</span>
  </div>

  <!-- Métricas principales -->
  <div class="row g-3 mb-4">
    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Secretario'))
    <div class="col-md-3 col-6">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="avatar bg-label-primary rounded-circle"><i class="bx bx-user fs-2"></i></span>
          <div>
            <div class="text-muted small">Clientes activos</div>
            <div class="fs-4 fw-bold">{{ number_format($clientesActivos) }}</div>
          </div>
        </div>
      </div>
    </div>
    @endif
    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Bodega'))
    <div class="col-md-3 col-6">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="avatar bg-label-success rounded-circle"><i class="bx bx-box fs-2"></i></span>
          <div>
            <div class="text-muted small">Productos en stock</div>
            <div class="fs-4 fw-bold">{{ number_format($totalProductos) }}</div>
          </div>
        </div>
      </div>
    </div>
    @endif
    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Ventas'))
    <div class="col-md-3 col-6">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="avatar bg-label-warning rounded-circle"><i class="bx bx-file fs-2"></i></span>
          <div>
            <div class="text-muted small">Facturas del mes</div>
            <div class="fs-4 fw-bold">{{ number_format($facturasMes) }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <span class="avatar bg-label-cyan rounded-circle"><i class="bx bx-dollar fs-2"></i></span>
          <div>
            <div class="text-muted small">Ventas del mes</div>
            <div class="fs-4 fw-bold">${{ number_format($ventasMes, 2) }}</div>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>

  <!-- Gráfica de ventas y widgets -->
  @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Ventas'))
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="bx bx-bar-chart-alt-2 me-2"></i> Ventas del mes</h5>
          <span class="badge bg-label-success">+{{ $crecimiento ?? '78%' }} crecimiento</span>
        </div>
        <div class="card-body">
          <div id="sales-chart" class="chart-container mb-3"></div>
          <div class="row g-2">
            <div class="col-md-6">
              <div id="gauge-growth"></div>
            </div>
            <div class="col-md-6">
              <div id="pie-ventas"></div>
            </div>
          </div>
          <div class="row g-2 mt-3">
            <div class="col-md-6">
              <div class="card mini-widget metric-blue mb-2">
                <div class="card-body d-flex align-items-center gap-2">
                  <i class="bx bx-user-check fs-3"></i>
                  <div>
                    <div class="mini-widget-label">Ticket promedio</div>
                    <div class="mini-widget-value">${{ number_format($ticketPromedio ?? 0, 2) }}</div>
                  </div>
                </div>
              </div>
              <div class="card mini-widget metric-yellow mb-2">
                <div class="card-body d-flex align-items-center gap-2">
                  <i class="bx bx-calendar-star fs-3"></i>
                  <div>
                    <div class="mini-widget-label">Mejor día</div>
                    <div class="mini-widget-value">{{ $mejorDia ?? '-' }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card mini-widget metric-green mb-2">
                <div class="card-body d-flex align-items-center gap-2">
                  <i class="bx bx-cube fs-3"></i>
                  <div>
                    <div class="mini-widget-label">Más vendido</div>
                    <div class="mini-widget-value">{{ $productoTop ?? '-' }}</div>
                  </div>
                </div>
              </div>
              <div class="card mini-widget metric-cyan mb-2">
                <div class="card-body d-flex align-items-center gap-2">
                  <i class="bx bx-credit-card fs-3"></i>
                  <div>
                    <div class="mini-widget-label">% Tarjeta</div>
                    <div class="mini-widget-value">{{ $porcentajeTarjeta ?? 0 }}%</div>
                  </div>
                </div>
              </div>
              <div class="card mini-widget metric-red mb-2">
                <div class="card-body d-flex align-items-center gap-2">
                  <i class="bx bx-money fs-3"></i>
                  <div>
                    <div class="mini-widget-label">% Efectivo</div>
                    <div class="mini-widget-value">{{ $porcentajeEfectivo ?? 0 }}%</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-4">
            <div class="mini-table-title mb-2"><i class="bx bx-list-ul"></i> Últimas facturas</div>
            <div class="table-responsive">
              <table class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th>Cliente</th>
                    <th>Factura</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($ultimasVentas ?? [] as $venta)
                  <tr>
                    <td>{{ $venta->cliente->nombre ?? 'Cliente eliminado' }}</td>
                    <td>#{{ $venta->factura_id ?? '-' }}</td>
                    <td>${{ number_format($venta->monto ?? 0, 2) }}</td>
                    <td>{{ $venta->fecha ?? '-' }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bx bx-error"></i> Bajo stock</span>
          <span class="badge bg-label-warning">Alerta</span>
        </div>
        <ul class="list-group list-group-flush">
          @foreach($productosBajoStock->take(5) as $producto)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="bx bx-cube me-2"></i>{{ $producto->nombre }}</span>
            <span class="badge bg-label-danger">Stock: {{ $producto->stock }}</span>
          </li>
          @endforeach
        </ul>
      </div>
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bx bx-star"></i> Top productos vendidos</span>
          <span class="badge bg-label-success">Top</span>
        </div>
        <ul class="list-group list-group-flush">
          @foreach($topProductos->take(5) as $producto)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="bx bx-cube me-2"></i>{{ $producto->producto->nombre ?? 'Producto eliminado' }}</span>
            <span class="badge bg-label-success">Vendidos: {{ $producto->total_vendido }}</span>
          </li>
          @endforeach
        </ul>
      </div>
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bx bx-file"></i> Facturas recientes</span>
          <span class="badge bg-label-info">Últimas</span>
        </div>
        <ul class="list-group list-group-flush">
          @foreach($facturasRecientes->take(5) as $factura)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="bx bx-receipt me-2"></i>Factura #{{ $factura->id }}</span>
            <span class="dashboard-list-value">{{ $factura->cliente->nombre ?? 'Cliente eliminado' }} - ${{ number_format($factura->total, 2) }}</span>
            <span class="badge bg-label-primary">${{ number_format($factura->total, 2) }}</span>
          </li>
          @endforeach
        </ul>
      </div>
      @if(Auth::user()->hasRole('Administrador'))
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bx bx-history"></i> Últimas acciones (Auditoría)</span>
          <span class="badge bg-label-dark">Logs</span>
        </div>
        <ul class="list-group list-group-flush">
          @foreach($logsAuditoria->take(5) as $log)
          <li class="list-group-item d-flex flex-column">
            <span><i class="bx bx-user me-2"></i>{{ $log->user->name ?? 'N/A' }} <span class="text-muted small">({{ $log->action }})</span></span>
            <span class="text-muted small">{{ $log->description }}<br>{{ $log->created_at->diffForHumans() }}</span>
          </li>
          @endforeach
        </ul>
      </div>
      @endif
    </div>
  </div>
  @endif

  <!-- Tabs de ingresos/gastos/profit (solo Admin) -->
  @if(Auth::user()->hasRole('Administrador'))
  <div class="card mt-4">
    <ul class="nav nav-pills mb-2" id="tab-ingresos" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="income-tab" data-bs-toggle="pill" data-bs-target="#income" type="button" role="tab">Ingresos</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="expenses-tab" data-bs-toggle="pill" data-bs-target="#expenses" type="button" role="tab">Gastos</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="profit-tab" data-bs-toggle="pill" data-bs-target="#profit" type="button" role="tab">Profit</button>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="income" role="tabpanel">
        <div class="dashboard-tab-metric metric-blue">
          <i class="bx bx-dollar"></i>
          <div>
            <div class="dashboard-tab-value">${{ number_format($ingresosMes ?? 0, 2) }}</div>
            <div class="dashboard-tab-label">Total ingresos</div>
          </div>
          <span class="badge bg-label-success ms-auto">+42.9%</span>
        </div>
      </div>
      <div class="tab-pane fade" id="expenses" role="tabpanel">
        <div class="dashboard-tab-metric metric-red">
          <i class="bx bx-credit-card"></i>
          <div>
            <div class="dashboard-tab-value">${{ number_format($gastosMes ?? 0, 2) }}</div>
            <div class="dashboard-tab-label">Total gastos</div>
          </div>
          <span class="badge bg-label-danger ms-auto">-15.2%</span>
        </div>
      </div>
      <div class="tab-pane fade" id="profit" role="tabpanel">
        <div class="dashboard-tab-metric metric-green">
          <i class="bx bx-trending-up"></i>
          <div>
            <div class="dashboard-tab-value">${{ number_format($profitMes ?? 0, 2) }}</div>
            <div class="dashboard-tab-label">Profit</div>
          </div>
          <span class="badge bg-label-success ms-auto">+18.2%</span>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection

@push('scripts')
<script src="/sneat/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script>
    // Gráfico de ventas diarias: Este mes vs Mes pasado
    const sales_chart_options = {
        series: [
            {
                name: 'Este mes',
                data: @json($ventasEsteMes)
            },
            {
                name: 'Mes pasado',
                data: @json($ventasMesPasado)
            }
        ],
        chart: {
            type: 'line',
            height: 180,
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            }
        },
        colors: ['#696cff', '#03c3ec'],
        stroke: { curve: 'smooth', width: 3 },
        grid: { borderColor: '#e9ecef' },
        markers: { size: 6 },
        xaxis: {
            categories: @json($dias),
            labels: { style: { colors: '#6c757d', fontSize: '12px' } }
        },
        yaxis: {
            labels: {
                formatter: function (val) { return '$' + val.toFixed(0); },
                style: { colors: '#6c757d', fontSize: '12px' }
            }
        },
        legend: {
            position: 'bottom',
            labels: { colors: '#6c757d' }
        },
        tooltip: {
            theme: 'dark',
            y: { formatter: function (val) { return '$' + val.toFixed(2); } }
        }
    };
    const salesChartElement = document.querySelector('#sales-chart');
    if (salesChartElement) {
        const sales_chart = new ApexCharts(salesChartElement, sales_chart_options);
    sales_chart.render();
    }

    // Gauge de crecimiento
    const gaugeOptions = {
      chart: { type: 'radialBar', height: 100, sparkline: { enabled: true } },
      series: [{{ isset($crecimiento) ? intval($crecimiento) : 78 }}],
      labels: ['Crecimiento'],
      colors: ['#71dd37'],
      plotOptions: { radialBar: { hollow: { size: '60%' }, dataLabels: { value: { fontSize: '16px' } } } }
    };
    const gaugeElement = document.querySelector('#gauge-growth');
    if (gaugeElement) {
      const gauge = new ApexCharts(gaugeElement, gaugeOptions);
      gauge.render();
    }

    // Pie chart de ventas
    const pieOptions = {
      chart: { type: 'pie', height: 100, sparkline: { enabled: true } },
      labels: ['Efectivo', 'Tarjeta', 'Transferencia'],
      series: [{{ $ventasEfectivo ?? 40 }}, {{ $ventasTarjeta ?? 35 }}, {{ $ventasTransferencia ?? 25 }}],
      colors: ['#03c3ec', '#696cff', '#ffab00'],
      legend: { show: false }
    };
    const pieElement = document.querySelector('#pie-ventas');
    if (pieElement) {
      const pie = new ApexCharts(pieElement, pieOptions);
      pie.render();
    }
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="/sneat/assets/css/demo.css" />
<link rel="stylesheet" href="/sneat/assets/vendor/css/core.css" />
<link rel="stylesheet" href="/sneat/assets/vendor/fonts/iconify-icons.css" />
<link rel="stylesheet" href="/sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="/sneat/assets/vendor/libs/apex-charts/apex-charts.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
.dashboard-admin-grid {
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.dashboard-admin-welcome {
  background: linear-gradient(90deg, #f8fafd 60%, #e6f7ff 100%);
  border-radius: 18px;
  box-shadow: 0 2px 12px 0 rgba(80,80,120,0.07);
  position: relative;
  min-height: 120px;
    display: flex;
    align-items: center;
  padding: 0;
}
.dashboard-admin-welcome-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 24px 32px;
}
.dashboard-admin-welcome-icon {
    display: flex;
    align-items: center;
  justify-content: center;
  height: 90px;
  width: 90px;
}
.dashboard-admin-badge {
    position: absolute;
  top: 18px;
  right: 24px;
  font-size: 1rem;
  padding: 6px 16px;
  border-radius: 12px;
}
.dashboard-admin-metrics {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 12px;
}
.dashboard-admin-metric {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 18px 18px 18px 24px;
  border-radius: 16px;
  min-height: 90px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 8px 0 rgba(80,80,120,0.06);
  transition: box-shadow 0.2s;
  background: #fff;
}
.dashboard-admin-metric:hover {
  box-shadow: 0 4px 16px 0 rgba(80,80,120,0.13);
}
.metric-icon-bg {
  font-size: 2.2rem;
  opacity: 0.18;
  position: absolute;
  right: 18px;
  bottom: 10px;
  z-index: 0;
}
.metric-content {
  z-index: 1;
}
.metric-label {
  font-size: 0.95rem;
  color: #6c757d;
  margin-bottom: 2px;
}
.metric-value {
    font-size: 1.5rem;
  font-weight: 700;
  color: #222;
}
.metric-badge {
  font-size: 0.85rem;
  margin-top: 2px;
}
.metric-blue { background: #e6f7ff; }
.metric-green { background: #e8f8f5; }
.metric-yellow { background: #fff9e6; }
.metric-cyan { background: #e6faff; }
.metric-red { background: #ffe6e6; }
.metric-purple { background: #f3e6ff; }

.dashboard-admin-main {
  display: flex;
  gap: 18px;
}
.dashboard-admin-chart {
  flex: 2 1 0%;
  border-radius: 16px;
  box-shadow: 0 2px 8px 0 rgba(80,80,120,0.06);
  padding: 0 0 18px 0;
  display: flex;
  flex-direction: column;
  min-height: 320px;
  background: #fff;
}
.dashboard-admin-chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
  padding: 18px 24px 0 24px;
}
.dashboard-admin-chart-title {
  font-size: 1.1rem;
    font-weight: 600;
  color: #222;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.dashboard-admin-chart-body {
  padding: 0 24px 0 24px;
}
.dashboard-admin-chart-widgets {
  display: flex;
  gap: 18px;
  margin-top: 8px;
}
.dashboard-gauge, .dashboard-pie {
  width: 100px;
  height: 100px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 1px 4px 0 rgba(80,80,120,0.06);
    display: flex;
    align-items: center;
  justify-content: center;
}
.dashboard-admin-side-widgets {
  flex: 1 1 0%;
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.dashboard-list {
  border-radius: 16px;
  box-shadow: 0 2px 8px 0 rgba(80,80,120,0.06);
  min-height: 220px;
    display: flex;
  flex-direction: column;
  background: #fff;
}
.dashboard-list-header {
    display: flex;
    align-items: center;
  justify-content: space-between;
  padding: 16px 20px 0 20px;
  font-weight: 600;
  font-size: 1rem;
}
.dashboard-list-body {
  list-style: none;
  padding: 8px 20px 16px 20px;
  margin: 0;
}
.dashboard-list-body li {
    display: flex;
    align-items: center;
  gap: 8px;
  padding: 7px 0;
  border-bottom: 1px solid #f2f2f2;
  font-size: 0.98rem;
}
.dashboard-list-body li:last-child { border-bottom: none; }
.dashboard-list-icon {
  font-size: 1.2rem;
  color: #bdbdbd;
}
.dashboard-list-label {
  flex: 1;
  color: #222;
}
.dashboard-list-value {
  color: #6c757d;
  font-size: 0.95rem;
}
.dashboard-admin-tabs {
  border-radius: 16px;
  box-shadow: 0 2px 8px 0 rgba(80,80,120,0.06);
  min-height: 220px;
  display: flex;
  flex-direction: column;
  padding: 0 0 18px 0;
  background: #fff;
}
.dashboard-tab-metric {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 18px 24px;
  border-radius: 12px;
  background: #f8fafd;
  margin-top: 12px;
}
.dashboard-tab-metric i {
  font-size: 2rem;
  opacity: 0.22;
}
.dashboard-tab-value {
  font-size: 1.3rem;
  font-weight: 700;
  color: #222;
}
.dashboard-tab-label {
  font-size: 0.95rem;
  color: #6c757d;
}
.dashboard-admin-chart-widgets-extra {
  display: flex;
  gap: 14px;
  margin-top: 18px;
  flex-wrap: wrap;
}
.mini-widget {
    display: flex;
    align-items: center;
  gap: 10px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 1px 4px 0 rgba(80,80,120,0.06);
  padding: 12px 18px;
  min-width: 160px;
  min-height: 60px;
    font-size: 1rem;
  flex: 1 1 160px;
}
.mini-widget i {
  font-size: 2rem;
  opacity: 0.22;
}
.mini-widget-label {
  font-size: 0.92rem;
  color: #6c757d;
}
.mini-widget-value {
  font-size: 1.15rem;
  font-weight: 600;
  color: #222;
}
.dashboard-admin-chart-table {
  margin-top: 18px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 1px 4px 0 rgba(80,80,120,0.06);
  padding: 12px 18px 8px 18px;
}
.mini-table-title {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.mini-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.97rem;
}
.mini-table th, .mini-table td {
  padding: 6px 10px;
  text-align: left;
  border-bottom: 1px solid #f2f2f2;
}
.mini-table th {
    color: #6c757d;
  font-weight: 600;
  background: #f8fafd;
}
.mini-table tr:last-child td {
  border-bottom: none;
}
@media (max-width: 1400px) {
  .dashboard-admin-metrics { grid-template-columns: repeat(3, 1fr); }
  .dashboard-admin-main { flex-direction: column; }
  .dashboard-admin-chart, .dashboard-admin-side-widgets { width: 100%; }
}
@media (max-width: 992px) {
  .dashboard-admin-metrics { grid-template-columns: repeat(2, 1fr); }
  .dashboard-admin-main { flex-direction: column; }
  .dashboard-admin-chart, .dashboard-admin-side-widgets { width: 100%; }
}
@media (max-width: 768px) {
  .dashboard-admin-grid { gap: 10px; }
  .dashboard-admin-metrics { grid-template-columns: 1fr; gap: 10px; }
  .dashboard-admin-main { flex-direction: column; gap: 10px; }
  .dashboard-admin-chart, .dashboard-admin-side-widgets { width: 100%; }
}
</style>
@endpush

