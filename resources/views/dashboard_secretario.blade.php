@extends('layouts.app')
@section('title', 'Dashboard Secretario')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center mb-4">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown mb-4">
        <div class="card-body text-center">
          <h2 class="fw-bold mb-2" style="color: #1976d2;">¡Bienvenido, {{ Auth::user()->name }}!</h2>
          <p class="text-muted mb-3">Desde aquí puedes gestionar usuarios, ver reportes y acceder a funciones clave del sistema.</p>
        </div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft">
            <div class="mb-2"><i class="bx bx-group fs-1 text-success"></i></div>
            <div class="fw-bold fs-4">{{ $usuariosActivos ?? 0 }}</div>
            <div class="text-muted">Usuarios activos</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight">
            <div class="mb-2"><i class="bx bx-user fs-1 text-primary"></i></div>
            <div class="fw-bold fs-4">{{ $clientesActivos ?? 0 }}</div>
            <div class="text-muted">Clientes activos</div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <a href="{{ route('users.index') }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInLeft h-100">
              <div class="mb-3"><i class="bx bx-group fs-1 text-success"></i></div>
              <h5 class="fw-bold mb-2">Gestión de Usuarios</h5>
              <p class="text-muted mb-0">Accede al módulo de usuarios para crear, editar y administrar cuentas.</p>
            </div>
          </a>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm border-0 rounded-3 text-center p-4 animate__animated animate__fadeInRight h-100">
            <div class="mb-3"><i class="bx bx-bell bx-tada fs-1 text-warning"></i></div>
            <h5 class="fw-bold mb-2">Nuevas funciones próximamente</h5>
            <p class="text-muted mb-0">Pronto podrás acceder a reportes avanzados y más herramientas.</p>
          </div>
        </div>
      </div>
      <div class="card shadow-sm border-0 rounded-3 text-center p-4 mt-4 animate__animated animate__fadeInUp">
        <div class="mb-2"><i class="bx bx-info-circle fs-2 text-info"></i></div>
        <h6 class="fw-bold">¿Necesitas ayuda?</h6>
        <p class="text-muted mb-0">Consulta el manual o <a href="mailto:soporte@tusistema.com">contacta soporte</a>.</p>
      </div>
    </div>
  </div>
</div>
@endsection 