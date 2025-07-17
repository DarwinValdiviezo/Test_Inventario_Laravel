@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container-xxl min-vh-100 d-flex align-items-center justify-content-center" style="background: #f8f9fa;">
  <div class="row w-100 justify-content-center">
    <div class="col-md-6 col-lg-5 col-12">
      <div class="card shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown" style="background: #fff;">
        <div class="card-body p-5">
          <!-- Logo grande -->
          <div class="text-center mb-4">
          </div>
          <h2 class="fw-bold text-center mb-2" style="color: #1a237e;">Iniciar Sesión</h2>
          <p class="text-center text-muted mb-4">Accede a tu cuenta y gestiona tu inventario de forma profesional</p>

          @if($errors->has('danger'))
            <div class="alert alert-danger text-center fw-bold">
              {{ $errors->first('danger') }}
            </div>
          @endif

          <form id="formAuthentication" action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bx bx-envelope"></i></span>
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Ingresa tu email" autofocus value="{{ old('email') }}" />
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bx bx-lock"></i></span>
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Contraseña" />
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="mb-3 d-flex justify-content-between align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" />
                <label class="form-check-label" for="remember"> Recordarme </label>
              </div>
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-primary">¿Olvidaste tu contraseña?</a>
              @endif
            </div>
            <div class="mb-3">
              <button class="btn btn-primary btn-lg w-100 fw-bold" type="submit">Iniciar Sesión</button>
            </div>
          </form>

          <div class="text-center mt-3 mb-2">
            <span>¿Nuevo en la plataforma?</span>
            <a href="{{ route('register') }}" class="fw-bold text-primary ms-1">Crea una cuenta</a>
          </div>

          <div class="divider my-4">
          </div>

          @if(session('suspendida') || session('inactiva'))
            <div id="suspend-modal" class="modal fade show" tabindex="-1" style="display:block; background:rgba(0,0,0,0.7);" aria-modal="true" role="dialog">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown">
                  <div class="modal-body text-center p-5">
                    <div class="mb-3">
                      <i class="bx bx-block fs-1 text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-2 text-danger">Cuenta suspendida</h4>
                    <p class="mb-2">Tu cuenta ha sido suspendida por el administrador.</p>
                    <p class="mb-2"><strong>Motivo:</strong> {{ session('motivo') }}</p>
                    <p class="mb-3">Si crees que fue un error, contacta a soporte:<br>
                      <a href="mailto:darwinrvaldiviezo@gmail.com" class="fw-bold text-primary">darwinrvaldiviezo@gmail.com</a>
                    </p>
                    <div class="mb-3">
                      <span class="badge bg-danger fs-5">Cerrando sesión en <span id="countdown">5</span>...</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <script>
              let seconds = 5;
              const countdown = document.getElementById('countdown');
              const interval = setInterval(() => {
                seconds--;
                if(countdown) countdown.textContent = seconds;
                if(seconds <= 0) {
                  clearInterval(interval);
                  window.location.href = "{{ route('login') }}";
                }
              }, 1000);
              // Bloquear scroll y teclado
              document.body.style.overflow = 'hidden';
              document.addEventListener('keydown', function(e){ e.preventDefault(); });
            </script>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
