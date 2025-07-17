@extends('layouts.app')
@section('title', 'Dashboard Cliente')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center mb-4">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown mb-4">
        <div class="card-body text-center">
          <h2 class="fw-bold mb-2" style="color: #1976d2;">¡Bienvenido, {{ Auth::user()->name }}!</h2>
          <p class="text-muted mb-3">Este es tu panel de cliente. Aquí podrás ver tus compras, facturas y novedades.</p>
        </div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft">
            <div class="mb-2"><i class="bx bx-cart-alt fs-1 text-primary"></i></div>
            <div class="fw-bold fs-4">{{ $comprasCliente ?? 0 }}</div>
            <div class="text-muted">Compras realizadas</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInUp">
            <div class="mb-2"><i class="bx bx-file fs-1 text-success"></i></div>
            <div class="fw-bold fs-4">{{ $facturasCliente ?? 0 }}</div>
            <div class="text-muted">Facturas</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight">
            <div class="mb-2"><i class="bx bx-dollar-circle fs-1 text-warning"></i></div>
            <div class="fw-bold fs-4">${{ number_format($totalGastado ?? 0, 2) }}</div>
            <div class="text-muted">Total gastado</div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft">
            <div class="mb-3"><i class="bx bx-store-alt bx-spin fs-1 text-primary"></i></div>
            <h5 class="fw-bold mb-2">¡Próximamente Tienda Virtual!</h5>
            <p class="text-muted mb-0">Muy pronto podrás comprar productos directamente desde tu cuenta. ¡Espéralo!</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight">
            <div class="mb-3"><i class="bx bx-bulb fs-2 text-info"></i></div>
            <h6 class="fw-bold">Anuncios y novedades</h6>
            <p class="text-muted mb-0">¡Pronto más funciones y sorpresas para ti!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 