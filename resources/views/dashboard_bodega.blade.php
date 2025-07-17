@extends('layouts.app')
@section('title', 'Dashboard Bodega')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center mb-4">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown mb-4">
        <div class="card-body text-center">
          <h2 class="fw-bold mb-2" style="color: #1976d2;">¡Bienvenido, {{ Auth::user()->name }}!</h2>
          <p class="text-muted mb-3">Panel de gestión de bodega. Administra productos, stock y movimientos.</p>
        </div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft">
            <div class="mb-2"><i class="bx bx-box fs-1 text-primary"></i></div>
            <div class="fw-bold fs-4">{{ $totalProductos ?? 0 }}</div>
            <div class="text-muted">Productos en stock</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight">
            <div class="mb-2"><i class="bx bx-error fs-1 text-danger"></i></div>
            <div class="fw-bold fs-4">{{ $productosBajoStock->count() ?? 0 }}</div>
            <div class="text-muted">Productos bajo stock</div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <a href="{{ route('productos.index') }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft h-100">
              <div class="mb-3"><i class="bx bx-box bx-burst fs-1 text-primary"></i></div>
              <h5 class="fw-bold mb-2">Gestión de Productos</h5>
              <p class="text-muted mb-0">Administra el inventario, stock y movimientos de productos.</p>
            </div>
          </a>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight h-100">
            <div class="mb-3"><i class="bx bx-cog bx-spin fs-1 text-warning"></i></div>
            <h5 class="fw-bold mb-2">Módulo en mantenimiento</h5>
            <p class="text-muted mb-0">Pronto más funciones para gestión de bodega.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 