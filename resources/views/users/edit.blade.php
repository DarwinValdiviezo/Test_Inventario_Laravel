@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema / Usuarios /</span> Editar Usuario
          </h4>
          <p class="text-muted mb-0">Modificar información del usuario: {{ $user->name }}</p>
        </div>
        <div class="page-title-actions ms-auto">
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Volver
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulario de Edición -->
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-3">
              <div class="avatar-initial rounded-circle bg-label-warning">
                <i class="bx bx-user-check"></i>
              </div>
            </div>
            <div>
              <h5 class="card-title mb-0">Editar Usuario</h5>
              <small class="text-muted">Modifique los campos que desee actualizar</small>
            </div>
          </div>
        </div>
        
        <div class="card-body">
          <form method="POST" action="{{ route('users.update', $user) }}" id="editUserForm" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            
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
                           value="{{ old('name', $user->name) }}" 
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
                           value="{{ old('email', $user->email) }}" 
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
                  <i class="bx bx-lock me-2"></i> Seguridad (Opcional)
                </h6>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="password" class="form-label">
                    Nueva Contraseña
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-lock"></i>
                    </span>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Dejar vacío para mantener la actual"
                           minlength="8"
                           data-validation="optional|min:8">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                      <i class="bx bx-show"></i>
                    </button>
                    <div class="invalid-feedback" id="password-error"></div>
                  </div>
                  
                  <!-- Indicador de fortaleza de contraseña -->
                  <div class="password-strength mt-2" id="passwordStrengthContainer" style="display: none;">
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
                    Confirmar Contraseña
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bx bx-lock-alt"></i>
                    </span>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control"
                           placeholder="Repita la nueva contraseña"
                           data-validation="optional|match:password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                      <i class="bx bx-show"></i>
                    </button>
                    <div class="invalid-feedback" id="password_confirmation-error"></div>
                  </div>
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> Debe coincidir con la nueva contraseña
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
                            data-validation="required"
                            @if($user->getRoleNames()->contains('Administrador')) disabled @endif>
                      <option value="">Seleccione un estado</option>
                      <option value="activo" {{ old('estado', $user->estado) == 'activo' ? 'selected' : '' }}>
                        <i class="bx bx-check-circle"></i> Activo
                      </option>
                      <option value="inactivo" {{ old('estado', $user->estado) == 'inactivo' ? 'selected' : '' }}>
                        <i class="bx bx-x-circle"></i> Inactivo
                      </option>
                    </select>
                    <div class="invalid-feedback" id="estado-error"></div>
                  </div>
                  <div class="form-text">
                    <i class="bx bx-info-circle me-1"></i> Los usuarios inactivos no pueden acceder al sistema
                    @if($user->getRoleNames()->contains('Administrador'))
                      <br><span class="text-warning"><i class="bx bx-lock me-1"></i> No se puede cambiar el estado de un administrador</span>
                    @endif
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
                            data-validation="required"
                            @if($user->getRoleNames()->contains('Administrador')) disabled @endif>
                      <option value="">Seleccione un rol</option>
                      @foreach($roles as $role)
                        <option value="{{ $role->name }}" 
                                {{ in_array($role->name, old('roles', $user->getRoleNames()->toArray())) ? 'selected' : '' }}
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
                    @if($user->getRoleNames()->contains('Administrador'))
                      <br><span class="text-warning"><i class="bx bx-lock me-1"></i> No se puede cambiar el rol de un administrador</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <!-- Información del Usuario -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="card border-info">
                  <div class="card-header bg-label-info">
                    <h6 class="mb-0">
                      <i class="bx bx-info-circle me-2"></i> Información del Usuario
                    </h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                          <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial rounded-circle bg-label-primary">
                              {{ substr($user->name, 0, 1) }}
                            </div>
                          </div>
                          <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">ID: {{ $user->id }}</small>
                          </div>
                        </div>
                        
                        <div class="mb-2">
                          <span class="text-muted small">Fecha de Registro:</span>
                          <span class="fw-bold">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        
                        <div class="mb-2">
                          <span class="text-muted small">Última Actualización:</span>
                          <span class="fw-bold">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                      </div>
                      
                      <div class="col-md-6">
                        <div class="mb-2">
                          <span class="text-muted small">Email Verificado:</span>
                          @if($user->email_verified_at)
                            <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Sí</span>
                          @else
                            <span class="badge bg-warning text-dark"><i class="bx bx-exclamation-triangle me-1"></i>No</span>
                          @endif
                        </div>
                        
                        <div class="mb-2">
                          <span class="text-muted small">Estado Actual:</span>
                          @if($user->estado == 'activo')
                            <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i>Activo</span>
                          @else
                            <span class="badge bg-danger"><i class="bx bx-x-circle me-1"></i>Inactivo</span>
                          @endif
                        </div>
                        
                        <div class="mb-2">
                          <span class="text-muted small">Roles Actuales:</span>
                          <div class="mt-1">
                            @foreach($user->getRoleNames() as $roleName)
                              <span class="badge bg-info me-1">{{ $roleName }}</span>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Información de Roles -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="card border-warning">
                  <div class="card-header bg-label-warning">
                    <h6 class="mb-0">
                      <i class="bx bx-shield me-2"></i> Roles Disponibles
                    </h6>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      @foreach($roles as $role)
                        <div class="col-md-6 mb-3">
                          <div class="d-flex align-items-center p-2 border rounded {{ in_array($role->name, $user->getRoleNames()->toArray()) ? 'border-success bg-light-success' : '' }}">
                            <div class="avatar avatar-sm me-3">
                              <div class="avatar-initial rounded-circle bg-label-{{ $role->name == 'Administrador' ? 'danger' : ($role->name == 'Secretario' ? 'warning' : ($role->name == 'Ventas' ? 'info' : ($role->name == 'Bodega' ? 'success' : 'primary'))) }}">
                                {{ substr($role->name, 0, 1) }}
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-0">{{ $role->name }}</h6>
                              <small class="text-muted">
                                {{ $role->description ?? 'Sin descripción disponible' }}
                              </small>
                            </div>
                            @if(in_array($role->name, $user->getRoleNames()->toArray()))
                              <span class="badge bg-success"><i class="bx bx-check me-1"></i>Asignado</span>
                            @endif
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
                      Confirmo que he verificado la información y que soy responsable de los cambios realizados. 
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
                      <i class="bx bx-refresh me-1"></i> Restablecer
                    </button>
                    <button type="submit" class="btn btn-warning" id="submitBtn">
                      <i class="bx bx-check-circle me-1"></i> Actualizar Usuario
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
          <i class="bx bx-file-text me-2"></i> Términos y Condiciones de Edición
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h6>Política de Edición de Usuarios</h6>
        <ul>
          <li>El administrador es responsable de la información modificada</li>
          <li>Los cambios en roles afectarán los permisos del usuario</li>
          <li>Se debe verificar la identidad antes de realizar cambios</li>
          <li>Los cambios de contraseña requieren confirmación</li>
          <li>Se debe notificar al usuario sobre cambios importantes</li>
        </ul>
        
        <h6>Restricciones Especiales</h6>
        <ul>
          <li>No se puede modificar el rol de un administrador</li>
          <li>No se puede desactivar un administrador</li>
          <li>Los cambios de contraseña son opcionales</li>
          <li>Se debe mantener la seguridad del sistema</li>
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
  // Crear instancia global
  window.userEditManager = UserEditManager.getInstance();

  // Eliminar notificaciones visuales y alertas de sesión y errores

  // Eliminar cualquier referencia a notificaciones visuales y mensajes de error de validación

  // Eliminar notificaciones visuales y alertas de sesión y errores
});
</script>
@endpush

@push('styles')
<style>
/* Estilos específicos para el formulario de edición */
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

.border-success {
  border-color: #198754 !important;
}

.bg-light-success {
  background-color: rgba(25, 135, 84, 0.1) !important;
}

/* Estilos para información del usuario */
.avatar-initial {
  font-weight: 600;
  font-size: 0.875rem;
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