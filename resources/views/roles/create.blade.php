@extends('layouts.app')

@section('title', 'Nuevo Rol')
@section('page-title', 'Nuevo Rol')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}"><i class="bi bi-shield-lock"></i> Roles</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-plus-circle"></i> Nuevo Rol</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card card-outline card-success shadow-lg">
                <div class="card-header bg-gradient-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-plus-circle-fill me-2"></i> 
                        Crear Nuevo Rol
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('roles.store') }}" autocomplete="off" id="rolForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Información del Rol -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-label fw-bold">
                                        <i class="bi bi-shield me-1 text-success"></i>
                                        Nombre del Rol <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ej: Supervisor, Gerente, etc."
                                           required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        No puede ser: Administrador, Ventas, o cliente (roles del sistema).
                                    </small>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="description" class="form-label fw-bold">
                                        <i class="bi bi-card-text me-1 text-success"></i>
                                        Descripción
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                              class="form-control @error('description') is-invalid @enderror" 
                                              placeholder="Descripción opcional del rol">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Seguridad -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-shield-exclamation fs-4"></i>
                                        <div>
                                            <strong>Información de Seguridad:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Los roles del sistema (Administrador, Ventas, Cliente) no se pueden crear ni eliminar</li>
                                                <li>Los roles personalizados se pueden eliminar solo si no tienen usuarios asignados</li>
                                                <li>Todos los cambios se registran en la auditoría del sistema</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campos de Seguridad -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="password" class="form-label fw-bold">
                                        <i class="bi bi-lock me-1 text-success"></i>
                                        Contraseña de Administrador <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                               placeholder="Tu contraseña actual"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Necesaria para confirmar la creación del rol.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="observacion" class="form-label fw-bold">
                                        <i class="bi bi-chat-left-text me-1 text-success"></i>
                                        Observación <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="observacion" id="observacion" rows="3"
                                              class="form-control @error('observacion') is-invalid @enderror" 
                                              placeholder="Motivo de la creación del rol (mínimo 10 caracteres)"
                                              required>{{ old('observacion') }}</textarea>
                                    @error('observacion')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Se registrará en la auditoría del sistema.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <small><i class="bi bi-info-circle me-1"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-lg me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-lg me-1"></i> Crear Rol
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card-outline.card-success {
    border-top: 3px solid #28a745;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.form-control-lg {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.2s ease-in-out;
}

.form-control-lg:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    transform: translateY(-1px);
}

.form-label {
    color: #495057;
    font-size: 0.95rem;
}

.btn-lg {
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s ease-in-out;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.breadcrumb {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem 1rem;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 1px solid #ffeaa7;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .card-footer .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-lg {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación en tiempo real
    const form = document.getElementById('rolForm');
    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    // Validación específica para observación
    const observacionInput = document.getElementById('observacion');
    observacionInput.addEventListener('input', function() {
        if (this.value.trim().length < 10 && this.value.trim().length > 0) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
    
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        toggleIcon.classList.toggle('bi-eye');
        toggleIcon.classList.toggle('bi-eye-slash');
    });
    
    // Validación de nombre de rol
    const nameInput = document.getElementById('name');
    nameInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        const rolesCriticos = ['administrador', 'ventas', 'cliente'];
        
        if (rolesCriticos.includes(value)) {
            this.classList.add('is-invalid');
            this.setCustomValidity('No se puede crear un rol con ese nombre. Es un rol crítico del sistema.');
        } else {
            this.classList.remove('is-invalid');
            this.setCustomValidity('');
        }
    });
});
</script>
@endsection 