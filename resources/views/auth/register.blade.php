@extends('layouts.guest')

@section('title', 'Registro')

@section('content')
<div class="container-xxl min-vh-100 d-flex align-items-center justify-content-center" style="background: #f8f9fa;">
  <div class="row w-100 justify-content-center">
    <div class="col-md-7 col-lg-6 col-12">
      <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown" style="background: #fff;">
        <div class="card-body p-5">
          <!-- Logo grande -->
          <div class="text-center mb-4">
            <img src="{{ asset('@logo.png') }}" alt="Logo del sistema" style="height: 80px;">
          </div>
          <h2 class="fw-bold text-center mb-2" style="color: #1a237e;">Crear Cuenta</h2>
          <p class="text-center text-muted mb-4">Regístrate y comienza a gestionar tu inventario de forma profesional</p>

          @if($errors->has('danger'))
            <div class="alert alert-danger text-center fw-bold">
              {{ $errors->first('danger') }}
            </div>
          @endif

          <form method="POST" action="{{ route('register') }}" id="registerForm" autocomplete="off">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Nombre completo</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bx bx-user"></i></span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Tu nombre" required autofocus />
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bx bx-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="correo@ejemplo.com" required />
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bx bx-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Contraseña" required />
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bx bx-lock"></i></span>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Repite tu contraseña" required />
                @error('password_confirmation')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Crear Cuenta</button>
            </div>
          </form>

          <div class="text-center mt-3">
            <span>¿Ya tienes una cuenta?</span>
            <a href="{{ route('login') }}" class="fw-bold text-primary ms-1">Inicia sesión aquí</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
