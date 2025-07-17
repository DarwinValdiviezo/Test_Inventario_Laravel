@extends('layouts.app')
@section('title', 'Dashboard Ventas')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center mb-4">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown mb-4">
        <div class="card-body text-center">
          <h2 class="fw-bold mb-2" style="color: #1976d2;">¡Bienvenido, {{ Auth::user()->name }}!</h2>
          <p class="text-muted mb-3">Panel de ventas. Emite facturas y consulta tus ventas del mes.</p>
        </div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft">
            <div class="mb-2"><i class="bx bx-file fs-1 text-success"></i></div>
            <div class="fw-bold fs-4">{{ $facturasMes ?? 0 }}</div>
            <div class="text-muted">Facturas del mes</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInUp">
            <div class="mb-2"><i class="bx bx-dollar-circle fs-1 text-warning"></i></div>
            <div class="fw-bold fs-4">${{ number_format($ventasMes ?? 0, 2) }}</div>
            <div class="text-muted">Ventas del mes</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight">
            <div class="mb-2"><i class="bx bx-user-check fs-1 text-primary"></i></div>
            <div class="fw-bold fs-4">${{ number_format($ticketPromedio ?? 0, 2) }}</div>
            <div class="text-muted">Ticket promedio</div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <a href="{{ route('facturas.index') }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft h-100">
              <div class="mb-3"><i class="bx bx-file bx-tada fs-1 text-success"></i></div>
              <h5 class="fw-bold mb-2">Facturación</h5>
              <p class="text-muted mb-0">Emite y consulta facturas de manera rápida y sencilla.</p>
            </div>
          </a>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight h-100">
            <div class="mb-3"><i class="bx bx-bulb bx-flashing fs-1 text-info"></i></div>
            <h5 class="fw-bold mb-2">Novedades</h5>
            <p class="text-muted mb-0">Pronto más herramientas para ventas y reportes.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 