@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema / Usuarios /</span> Crear Usuario
          </h4>
          <p class="text-muted mb-0">Crear un nuevo usuario en el sistema</p>
        </div>
        <div class="page-title-actions ms-auto">
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Volver
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulario de Creación -->
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-3">
              <div class="avatar-initial rounded-circle bg-label-primary">
                <i class="bx bx-user-plus"></i>
              </div>
            </div>
            <div>
              <h5 class="card-title mb-0">Crear Nuevo Usuario</h5>
              <small class="text-muted">Complete todos los campos requeridos</small>
            </div>
          </div>
        </div>
        
        <div class="card-body">
          <form method="POST" action="{{ route('users.store') }}" id="createUserForm" class="needs-validation" novalidate>
            @csrf
            
            <!-- Información Personal -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="fw-semibold mb-3">
                  <i class="bx bx-user me-2"></i> Información Personal
                </h6>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name" class="form-label">
                    Nombre Completo <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-user"></i>
                    </span>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus
                           placeholder="Ingrese el nombre completo"
                           minlength="2"
                           maxlength="255"
                           pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+"
                           data-validation="required|min:2|max:255|pattern">
                    <div class="invalid-feedback" id="name-error"></div>
                  </div>
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> Solo letras y espacios, mínimo 2 caracteres
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email" class="form-label">
                    Correo Electrónico <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-envelope"></i>
                    </span>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           required
                           placeholder="ejemplo@correo.com"
                           data-validation="required|email">
                    <div class="invalid-feedback" id="email-error"></div>
                  </div>
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> Debe ser un correo válido y único
                  </div>
                </div>
              </div>
            </div>

            <!-- Seguridad -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="fw-semibold mb-3">
                  <i class="bx bx-lock me-2"></i> Seguridad
                </h6>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="password" class="form-label">
                    Contraseña <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-lock"></i>
                    </span>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror"
                           required
                           minlength="8"
                           placeholder="Mínimo 8 caracteres"
                           data-validation="required|min:8">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                      <i class="bx bx-show"></i>
                    </button>
                    <div class="invalid-feedback" id="password-error"></div>
                  </div>
                  
                  <!-- Indicador de fortaleza de contraseña -->
                  <div class="password-strength mt-2">
                    <div class="progress" style="height: 4px;">
                      <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted" id="passwordStrengthText">Fortaleza de la contraseña</small>
                  </div>
                  
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> Mínimo 8 caracteres, incluir mayúsculas, minúsculas y números
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="password_confirmation" class="form-label">
                    Confirmar Contraseña <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-lock-alt"></i>
                    </span>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control"
                           required
                           placeholder="Repita la contraseña"
                           data-validation="required|match:password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                      <i class="bx bx-show"></i>
                    </button>
                    <div class="invalid-feedback" id="password_confirmation-error"></div>
                  </div>
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> Debe coincidir con la contraseña
                  </div>
                </div>
              </div>
            </div>

            <!-- Configuración -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="fw-semibold mb-3">
                  <i class="bx bx-cog me-2"></i> Configuración
                </h6>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="estado" class="form-label">
                    Estado <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-toggle-left"></i>
                    </span>
                    <select id="estado" 
                            name="estado" 
                            class="form-select @error('estado') is-invalid @enderror" 
                            required
                            data-validation="required">
                      <option value="">Seleccione un estado</option>
                      <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>
                        <i class="bx bx-check-circle"></i> Activo
                      </option>
                      <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>
                        <i class="bx bx-x-circle"></i> Inactivo
                      </option>
                    </select>
                    <div class="invalid-feedback" id="estado-error"></div>
                  </div>
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> Los usuarios inactivos no pueden acceder al sistema
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="roles" class="form-label">
                    Rol <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-shield"></i>
                    </span>
                    <select id="roles" 
                            name="roles[]" 
                            class="form-select @error('roles') is-invalid @enderror" 
                            required
                            data-validation="required">
                      <option value="">Seleccione un rol</option>
                      @foreach($roles as $role)
                        <option value="{{ $role->name }}" 
                                {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}
                                data-description="{{ $role->description ?? 'Sin descripción' }}">
                          {{ $role->name }}
                          @if($role->description)
                            - {{ $role->description }}
                          @endif
                        </option>
                      @endforeach
                    </select>
                    <div class="invalid-feedback" id="roles-error"></div>
                  </div>
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> El rol determina los permisos del usuario
                  </div>
                </div>
              </div>
            </div>

            <!-- Información de Roles -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="card border-info">
                  <div class="card-header bg-label-info">
                    <h6 class="mb-0">
                      <i class="bx bx-info-circle me-2"></i> Información de Roles Disponibles
                    </h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      @foreach($roles as $role)
                        <div class="col-md-6 mb-3">
                          <div class="d-flex align-items-center p-2 border rounded">
                            <div class="avatar avatar-sm me-3">
                              <div class="avatar-initial rounded-circle bg-label-{{ $role->name == 'Administrador' ? 'danger' : ($role->name == 'Secretario' ? 'warning' : ($role->name == 'Ventas' ? 'info' : ($role->name == 'Bodega' ? 'success' : 'primary'))) }}">
                                {{ substr($role->name, 0, 1) }}
                              </div>
                            </div>
                            <div>
                              <h6 class="mb-0">{{ $role->name }}</h6>
                              <small class="text-muted">
                                {{ $role->description ?? 'Sin descripción disponible' }}
                              </small>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Términos y Condiciones -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="terms" required>
                  <label class="form-check-label" for="terms">
                    <small>
                      Acepto que el usuario creado tendrá acceso al sistema según los permisos asignados y 
                      que soy responsable de la información proporcionada. 
                      <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#termsModal">
                        Ver términos completos
                      </a>
                    </small>
                  </label>
                  <div class="invalid-feedback">
                    Debe aceptar los términos y condiciones
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row">
              <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                  <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Cancelar
                  </a>
                  
                  <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-outline-warning" id="resetForm">
                      <i class="bx bx-refresh me-1"></i> Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                      <i class="bx bx-user-plus me-1"></i> Crear Usuario
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Términos y Condiciones -->
<div class="modal fade" id="termsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bx bx-file-text me-2"></i> Términos y Condiciones
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h6>Política de Creación de Usuarios</h6>
        <ul>
          <li>El administrador es responsable de la información proporcionada</li>
          <li>Los usuarios creados tendrán acceso según los roles asignados</li>
          <li>Se debe verificar la identidad del usuario antes de la creación</li>
          <li>Las contraseñas deben ser seguras y únicas</li>
          <li>Se debe notificar al usuario sobre su cuenta creada</li>
        </ul>
        
        <h6>Responsabilidades</h6>
        <ul>
          <li>Mantener la confidencialidad de las credenciales</li>
          <li>Reportar cualquier actividad sospechosa</li>
          <li>Actualizar información cuando sea necesario</li>
          <li>Cumplir con las políticas de seguridad</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Eliminar el contenedor de notificaciones visuales y scripts de notificaciones JS -->
@endsection

@push('scripts')
<script>
// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  // Eliminar notificaciones visuales y alertas de sesión y errores
  // Mostrar errores de validación
  @if($errors->any())
    @foreach($errors->all() as $error)
      // Eliminar el código de notificación visual
    @endforeach
  @endif
});
</script>
@endpush

@push('styles')
<style>
/* Estilos específicos para el formulario de creación */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.5rem;
}

.input-group-text {
  background-color: #f8f9fa;
  border-color: #e2e8f0;
}

.form-control:focus,
.form-select:focus {
  border-color: #696cff;
  box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
}

.is-valid {
  border-color: #198754 !important;
}

.is-invalid {
  border-color: #dc3545 !important;
}

.password-strength {
  margin-top: 0.5rem;
}

.progress {
  background-color: #e9ecef;
  border-radius: 0.375rem;
}

.progress-bar {
  transition: width 0.3s ease;
}

/* Estilos para las tarjetas de roles */
.role-card {
  transition: transform 0.2s ease;
}

.role-card:hover {
  transform: translateY(-2px);
}

/* Animaciones */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card {
  animation: fadeInUp 0.5s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .page-title-actions {
    margin-top: 1rem;
  }
  
  .d-flex.justify-content-between {
    flex-direction: column;
    gap: 1rem;
  }
  
  .d-flex.gap-2 {
    justify-content: center;
  }
}

/* Animaciones para notificaciones */
@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slideOutRight {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}

/* Estilos para notificaciones */
#notification-container {
  z-index: 9999;
}

.alert {
  border: none;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  animation: slideInRight 0.3s ease-out;
}

.alert-success {
  background: linear-gradient(135deg, #198754, #20c997);
  color: white;
}

.alert-error {
  background: linear-gradient(135deg, #dc3545, #fd7e14);
  color: white;
}

.alert-warning {
  background: linear-gradient(135deg, #ffc107, #fd7e14);
  color: #212529;
}

.alert-info {
  background: linear-gradient(135deg, #0dcaf0, #0d6efd);
  color: white;
}
</style>
@endpush 