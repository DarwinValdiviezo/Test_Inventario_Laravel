@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}"><i class="bi bi-people"></i> Clientes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('clientes.show', $cliente) }}"><i class="bi bi-person"></i> {{ $cliente->nombre }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="bi bi-pencil"></i> Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-12">
            <div class="card card-outline card-warning shadow-lg">
                <div class="card-header bg-gradient-warning text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-pencil-square me-2"></i> 
                        Editar Cliente: {{ $cliente->nombre }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-eye me-1"></i> Ver Cliente
                        </a>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('clientes.update', $cliente) }}" autocomplete="off" id="clienteForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="nombre" class="form-label fw-bold">
                                        <i class="bi bi-person me-1 text-warning"></i>
                                        Nombre Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nombre" id="nombre" 
                                           class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                           value="{{ old('nombre', $cliente->nombre) }}" 
                                           placeholder="Ingrese el nombre completo"
                                           required autofocus>
                                    @error('nombre')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="bi bi-envelope me-1 text-warning"></i>
                                        Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $cliente->email) }}" 
                                           placeholder="ejemplo@correo.com"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password" class="form-label fw-bold">
                                        <i class="bi bi-lock me-1 text-warning"></i>
                                        Nueva Contraseña
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                               placeholder="Dejar vacío para mantener la actual">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-left: none;">
                                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Deja este campo vacío si no deseas cambiar la contraseña actual.
                                    </small>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="telefono" class="form-label fw-bold">
                                        <i class="bi bi-telephone me-1 text-warning"></i>
                                        Teléfono
                                    </label>
                                    <input type="text" name="telefono" id="telefono" 
                                           class="form-control form-control-lg @error('telefono') is-invalid @enderror" 
                                           value="{{ old('telefono', $cliente->telefono) }}" 
                                           placeholder="0912345678">
                                    @error('telefono')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="direccion" class="form-label fw-bold">
                                        <i class="bi bi-geo-alt me-1 text-warning"></i>
                                        Dirección
                                    </label>
                                    <textarea name="direccion" id="direccion" rows="3"
                                              class="form-control @error('direccion') is-invalid @enderror" 
                                              placeholder="Ingrese la dirección completa">{{ old('direccion', $cliente->direccion) }}</textarea>
                                    @error('direccion')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="estado" class="form-label fw-bold">
                                        <i class="bi bi-toggle-on me-1 text-warning"></i>
                                        Estado del Cliente
                                    </label>
                                    <select name="estado" id="estado" 
                                            class="form-select form-select-lg @error('estado') is-invalid @enderror">
                                        <option value="activo" {{ old('estado', $cliente->estado) == 'activo' ? 'selected' : '' }}>
                                            <i class="bi bi-check-circle"></i> Activo
                                        </option>
                                        <option value="inactivo" {{ old('estado', $cliente->estado) == 'inactivo' ? 'selected' : '' }}>
                                            <i class="bi bi-x-circle"></i> Inactivo
                                        </option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">
                                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información del Cliente -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info border-0 shadow-sm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong><i class="bi bi-calendar me-1"></i> Creado:</strong><br>
                                            <small>{{ $cliente->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="col-md-3">
                                            <strong><i class="bi bi-clock me-1"></i> Última Actualización:</strong><br>
                                            <small>{{ $cliente->updated_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="col-md-3">
                                            <strong><i class="bi bi-receipt me-1"></i> Facturas:</strong><br>
                                            <small>{{ $cliente->facturas->count() }} facturas</small>
                                        </div>
                                        <div class="col-md-3">
                                            <strong><i class="bi bi-person-check me-1"></i> Estado Actual:</strong><br>
                                            <span class="badge bg-{{ $cliente->estado == 'activo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($cliente->estado) }}
                                            </span>
                                        </div>
                                    </div>
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
                                <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-lg me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-warning btn-lg" id="submitBtn">
                                    <i class="bi bi-check-lg me-1"></i> 
                                    <span class="btn-text">Actualizar Cliente</span>
                                    <span class="btn-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Actualizando...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container para notificaciones -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <!-- Las notificaciones se insertarán aquí -->
</div>

<style>
/* Estilos personalizados para el formulario */
.card-outline.card-warning {
    border-top: 3px solid #ffc107;
    border-radius: 12px;
    overflow: hidden;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.form-control-lg, .form-select-lg {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease-in-out;
    font-size: 1rem;
    padding: 0.75rem 1rem;
}

.form-control-lg:focus, .form-select-lg:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    transform: translateY(-1px);
}

.form-control:focus, .form-select:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.form-label {
    color: #495057;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

.btn-lg {
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease-in-out;
    padding: 0.75rem 1.5rem;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.breadcrumb {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    border: 1px solid #dee2e6;
}

.breadcrumb-item a {
    color: #ffc107;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #e0a800;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.card {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
}

.card-header {
    border-bottom: none;
    padding: 1.5rem;
}

.card-body {
    padding: 2rem;
}

.card-footer {
    border-top: 1px solid #dee2e6;
    padding: 1.5rem;
}

/* Estados de carga */
.btn-loading {
    display: inline-flex;
    align-items: center;
}

.d-none {
    display: none !important;
}

/* Responsive */
@media (max-width: 768px) {
    .card-footer .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-lg {
        width: 100%;
    }
    
    .card-body {
        padding: 1.5rem;
    }
}

/* Mejoras visuales adicionales */
.input-group .btn {
    border-radius: 0 8px 8px 0;
    border-left: none;
    transition: all 0.2s ease-in-out;
}

.input-group .btn:hover {
    background-color: #ffc107;
    color: white;
    border-color: #ffc107;
}

.input-group .form-control {
    border-radius: 8px 0 0 8px;
}

/* Estilos específicos para el toggle de contraseña */
#togglePassword {
    min-width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#togglePassword:focus {
    box-shadow: none;
    border-color: #ffc107;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

.form-select option {
    padding: 0.5rem;
}

/* Efectos hover mejorados */
.form-control:hover, .form-select:hover {
    border-color: #ffc107;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Animaciones suaves */
* {
    transition: all 0.2s ease-in-out;
}

/* Estilos para toasts personalizados */
.toast {
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.toast.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left: 4px solid #28a745;
}

.toast.error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left: 4px solid #dc3545;
}

.toast.warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 4px solid #ffc107;
}

.toast.info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left: 4px solid #17a2b8;
}

/* Alertas mejoradas */
.alert {
    border-radius: 8px;
    border: none;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('clienteForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    // Función para mostrar notificaciones (igual que en create)
    function showToast(type, title, message) {
        const container = document.querySelector('.toast-container');
        const toastId = 'toast-' + Date.now();
        
        const iconMap = {
            success: 'bi-check-circle-fill',
            error: 'bi-exclamation-triangle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        
        const colorMap = {
            success: 'success',
            error: 'danger',
            warning: 'warning',
            info: 'info'
        };
        
        const toast = document.createElement('div');
        toast.className = `toast ${type} show`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="toast-header">
                <i class="bi ${iconMap[type]} text-${colorMap[type]} me-2"></i>
                <strong class="me-auto">${title}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        container.appendChild(toast);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            const toastElement = document.getElementById(toastId);
            if (toastElement) {
                toastElement.remove();
            }
        }, 5000);
    }
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            // Limpiar errores cuando el usuario empiece a escribir
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const feedback = this.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.innerHTML = '';
                }
            }
        });
    });
    
    // Formato de teléfono
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            // Limitar a 10 dígitos para formato ecuatoriano
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            e.target.value = value;
        });
    }
    
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Cambiar el icono
            if (type === 'text') {
                toggleIcon.classList.remove('bi-eye-fill');
                toggleIcon.classList.add('bi-eye-slash-fill');
            } else {
                toggleIcon.classList.remove('bi-eye-slash-fill');
                toggleIcon.classList.add('bi-eye-fill');
            }
        });
    }
    
    // Envío del formulario con AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Mostrar estado de carga
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        submitBtn.disabled = true;
        
        // Recopilar datos del formulario
        const formData = new FormData(form);
        
        // Enviar con AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            return response.json().then(data => {
                return { status: response.status, data: data };
            });
        })
        .then(result => {
            if (result.status === 422) {
                // Error de validación
                showToast('error', 'Error de Validación', 'Por favor, corrige los errores en el formulario.');
                
                // Mostrar errores de validación
                if (result.data.errors) {
                    Object.keys(result.data.errors).forEach(field => {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${result.data.errors[field][0]}`;
                            }
                        }
                    });
                }
            } else if (result.data.success) {
                showToast('success', '¡Cliente Actualizado!', result.data.message);
                
                // Redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = result.data.redirect || '{{ route("clientes.show", $cliente) }}';
                }, 2000);
            } else {
                showToast('error', 'Error al actualizar cliente', result.data.message || 'Ha ocurrido un error inesperado.');
                
                // Mostrar errores de validación
                if (result.data.errors) {
                    Object.keys(result.data.errors).forEach(field => {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentElement.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${result.data.errors[field][0]}`;
                            }
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error de Conexión', 'No se pudo conectar con el servidor. Verifique su conexión.');
        })
        .finally(() => {
            // Restaurar estado del botón
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
            submitBtn.disabled = false;
        });
    });
    
    // Validación de email en tiempo real
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !isValidEmail(email)) {
                this.classList.add('is-invalid');
                const feedback = this.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Formato de email inválido';
                }
            }
        });
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Validación de contraseña en tiempo real
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            if (password.length > 0 && password.length < 6) {
                this.classList.add('is-invalid');
                const feedback = this.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>La contraseña debe tener al menos 6 caracteres';
                }
            } else if (password.length >= 6) {
                this.classList.remove('is-invalid');
            }
        });
    }
    
    // Efectos visuales adicionales
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection
