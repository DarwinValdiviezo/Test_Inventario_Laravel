<section>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">
                    <i class="bi bi-person me-2"></i>Nombre Completo
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       autofocus 
                       autocomplete="name"
                       placeholder="Ingrese su nombre completo">
                @error('name')
                    <div class="invalid-feedback">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">
                    <i class="bi bi-envelope me-2"></i>Correo Electrónico
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email', $user->email) }}" 
                       required 
                       autocomplete="username"
                       placeholder="ejemplo@correo.com">
                @error('email')
                    <div class="invalid-feedback">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Verificación de email --}}
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Correo no verificado.</strong> 
                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
        @csrf
                    <button type="submit" class="btn btn-warning btn-sm ms-2">
                        <i class="bi bi-envelope-paper me-1"></i>Reenviar correo de verificación
                    </button>
    </form>
            </div>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-check-circle-fill"></i>
                <div>
                    <strong>¡Correo enviado!</strong> Se ha enviado un nuevo enlace de verificación a su correo electrónico.
                </div>
            </div>
        @endif
            @endif

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-2"></i>Guardar Cambios
        </button>

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-0 py-2 px-3" 
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                 x-init="setTimeout(() => show = false, 3000)">
                <i class="bi bi-check-circle-fill"></i>
                <span class="small">{{ session('success') }}</span>
            </div>
            @endif
        </div>
</section>
