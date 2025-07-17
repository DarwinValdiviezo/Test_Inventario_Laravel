@extends('layouts.app')

@section('title', 'Detalle de Usuario')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema / Usuarios /</span> Detalle de Usuario
          </h4>
          <p class="text-muted mb-0">Información detallada del usuario: {{ $user->name }}</p>
        </div>
        <div class="page-title-actions ms-auto">
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Volver
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Información Principal del Usuario -->
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-lg me-3">
              <div class="avatar-initial rounded-circle bg-label-primary">
                {{ substr($user->name, 0, 1) }}
              </div>
            </div>
            <div>
              <h5 class="card-title mb-0">{{ $user->name }}</h5>
              <small class="text-muted">{{ $user->email }}</small>
            </div>
            <div class="ms-auto">
              @if($user->deleted_at)
                <span class="badge bg-dark fs-6">Eliminado</span>
              @elseif($user->pending_delete_at)
                @php
                  $fechaLimite = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(3);
                  $ahora = now();
                  $diff = $ahora->diff($fechaLimite);
                  $dias = $diff->d;
                  $horas = $diff->h;
                  $minutos = $diff->i;
                @endphp
                <span class="badge bg-warning text-dark fs-6">
                  <i class="bx bx-time me-1"></i>Pendiente ({{ $dias }}d {{ $horas }}h {{ $minutos }}m)
                </span>
              @elseif($user->estado == 'inactivo')
                <span class="badge bg-secondary fs-6">
                  <i class="bx bx-user-x me-1"></i>Inactivo
                </span>
              @else
                <span class="badge bg-success fs-6">
                  <i class="bx bx-user-check me-1"></i>Activo
                </span>
              @endif
            </div>
          </div>
        </div>
        
        <div class="card-body">
          <!-- Información Básica -->
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="fw-semibold mb-3">
                <i class="bx bx-info-circle me-2"></i> Información Básica
              </h6>
            </div>
            
            <div class="col-md-6">
              <div class="d-flex align-items-center p-3 border rounded mb-3">
                <div class="avatar avatar-sm me-3">
                  <div class="avatar-initial rounded-circle bg-label-info">
                    <i class="bx bx-id-card"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted">ID de Usuario</small>
                  <div class="fw-bold">{{ $user->id }}</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="d-flex align-items-center p-3 border rounded mb-3">
                <div class="avatar avatar-sm me-3">
                  <div class="avatar-initial rounded-circle bg-label-success">
                    <i class="bx bx-calendar"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted">Fecha de Registro</small>
                  <div class="fw-bold">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : 'N/A' }}</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="d-flex align-items-center p-3 border rounded mb-3">
                <div class="avatar avatar-sm me-3">
                  <div class="avatar-initial rounded-circle bg-label-warning">
                    <i class="bx bx-time"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted">Última Actualización</small>
                  <div class="fw-bold">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'N/A' }}</div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="d-flex align-items-center p-3 border rounded mb-3">
                <div class="avatar avatar-sm me-3">
                  <div class="avatar-initial rounded-circle bg-label-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                    <i class="bx bx-{{ $user->email_verified_at ? 'check-circle' : 'x-circle' }}"></i>
                  </div>
                </div>
                <div>
                  <small class="text-muted">Email Verificado</small>
                  <div class="fw-bold">
                    @if($user->email_verified_at)
                      <span class="badge bg-success">Sí</span>
                      <small class="text-muted ms-2">{{ $user->email_verified_at->format('d/m/Y H:i') }}</small>
                    @else
                      <span class="badge bg-warning text-dark">No</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Roles Asignados -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card border-primary">
                <div class="card-header bg-label-primary">
                  <h6 class="mb-0">
                    <i class="bx bx-shield me-2"></i> Roles Asignados
                  </h6>
                </div>
                <div class="card-body">
                  @if($user->getRoleNames()->count() > 0)
                    <div class="row">
                      @foreach($user->getRoleNames() as $role)
                        <div class="col-md-6 mb-3">
                          <div class="d-flex align-items-center p-2 border rounded">
                            <div class="avatar avatar-sm me-3">
                              <div class="avatar-initial rounded-circle bg-label-{{ $role == 'Administrador' ? 'danger' : ($role == 'Secretario' ? 'warning' : ($role == 'Ventas' ? 'info' : ($role == 'Bodega' ? 'success' : 'primary'))) }}">
                                {{ substr($role, 0, 1) }}
                              </div>
                            </div>
                            <div>
                              <h6 class="mb-0">{{ $role }}</h6>
                              <small class="text-muted">Rol asignado</small>
                            </div>
                            <span class="badge bg-success ms-auto">
                              <i class="bx bx-check me-1"></i>Activo
                            </span>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <div class="text-center py-4">
                      <i class="bx bx-shield-x display-4 text-muted"></i>
                      <p class="text-muted mt-2">No tiene roles asignados</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Información de Estado -->
          @if($user->deleted_at || $user->pending_delete_at)
            <div class="row mb-4">
              <div class="col-12">
                @if($user->deleted_at)
                  <div class="alert alert-dark d-flex align-items-center">
                    <i class="bx bx-info-circle fs-4 me-3"></i>
                    <div>
                      <h6 class="alert-heading mb-1">Usuario Eliminado</h6>
                      <p class="mb-0">Este usuario fue eliminado el {{ $user->deleted_at->format('d/m/Y H:i') }}</p>
                    </div>
                  </div>
                @elseif($user->pending_delete_at)
                  <div class="alert alert-warning d-flex align-items-center">
                    <i class="bx bx-time fs-4 me-3"></i>
                    <div>
                      <h6 class="alert-heading mb-1">Pendiente de Eliminación</h6>
                      <p class="mb-0">
                        Pendiente desde: {{ $user->pending_delete_at->format('d/m/Y H:i') }}
                        <br>
                        <small>Se eliminará definitivamente en {{ $dias }} día{{ $dias == 1 ? '' : 's' }} {{ $horas }}h {{ $minutos }}m</small>
                      </p>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          @endif

          <!-- Estadísticas del Usuario -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card border-info">
                <div class="card-header bg-label-info">
                  <h6 class="mb-0">
                    <i class="bx bx-bar-chart me-2"></i> Estadísticas del Usuario
                  </h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-3 mb-3">
                      <div class="text-center p-3 border rounded">
                        <i class="bx bx-calendar-check fs-1 text-success"></i>
                        <h5 class="mt-2 mb-1">{{ $user->created_at ? $user->created_at->diffForHumans() : 'N/A' }}</h5>
                        <small class="text-muted">Tiempo en el sistema</small>
                      </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                      <div class="text-center p-3 border rounded">
                        <i class="bx bx-time fs-1 text-warning"></i>
                        <h5 class="mt-2 mb-1">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'N/A' }}</h5>
                        <small class="text-muted">Última actividad</small>
                      </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                      <div class="text-center p-3 border rounded">
                        <i class="bx bx-shield fs-1 text-info"></i>
                        <h5 class="mt-2 mb-1">{{ $user->getRoleNames()->count() }}</h5>
                        <small class="text-muted">Roles asignados</small>
                      </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                      <div class="text-center p-3 border rounded">
                        <i class="bx bx-{{ $user->email_verified_at ? 'check-circle' : 'x-circle' }} fs-1 text-{{ $user->email_verified_at ? 'success' : 'danger' }}"></i>
                        <h5 class="mt-2 mb-1">{{ $user->email_verified_at ? 'Verificado' : 'Pendiente' }}</h5>
                        <small class="text-muted">Estado del email</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Acciones Disponibles -->
          <div class="row">
            <div class="col-12">
              <div class="card border-secondary">
                <div class="card-header bg-label-secondary">
                  <h6 class="mb-0">
                    <i class="bx bx-cog me-2"></i> Acciones Disponibles
                  </h6>
                </div>
                <div class="card-body">
                  <div class="d-flex flex-wrap gap-2">
                    @if(!$user->deleted_at)
                      <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                        <i class="bx bx-edit me-1"></i> Editar Usuario
                      </a>
                    @endif
                    
                    @if(!$user->deleted_at && !$user->pending_delete_at)
                      <form action="{{ route('users.toggleEstado', $user) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-{{ $user->estado == 'activo' ? 'secondary' : 'success' }}">
                          <i class="bx bx-{{ $user->estado == 'activo' ? 'user-x' : 'user-check' }} me-1"></i>
                          {{ $user->estado == 'activo' ? 'Desactivar' : 'Activar' }}
                        </button>
                      </form>
                      
                      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario{{ $user->id }}">
                        <i class="bx bx-trash me-1"></i> Eliminar
                      </button>
                    @elseif($user->pending_delete_at && !$user->deleted_at)
                      <form action="{{ route('users.cancelarBorradoCuenta', $user) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-success">
                          <i class="bx bx-undo me-1"></i> Cancelar Eliminación
                        </button>
                      </form>
                    @elseif($user->deleted_at)
                      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRestaurarUsuario{{ $user->id }}">
                        <i class="bx bx-refresh me-1"></i> Restaurar
                      </button>
                      
                      <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalEliminarDefinitivoUsuario{{ $user->id }}">
                        <i class="bx bx-x-circle me-1"></i> Eliminar Definitivamente
                      </button>
                    @endif
                    
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                      <i class="bx bx-arrow-back me-1"></i> Volver
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modales -->
@if(!$user->deleted_at && !$user->pending_delete_at)
  <!-- Modal Eliminar Usuario -->
  <div class="modal fade" id="modalEliminarUsuario{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form action="{{ route('users.destroy', $user) }}" method="POST" id="formEliminarUsuario{{ $user->id }}">
          @csrf
          @method('DELETE')
          <div class="modal-header bg-danger text-white">
            <div class="d-flex align-items-center">
              <i class="bx bx-trash fs-3 me-2"></i>
              <h5 class="modal-title mb-0">Eliminar Usuario</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <!-- Información del usuario a eliminar -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="card border-danger bg-light-danger">
                  <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <div class="avatar-initial rounded-circle bg-danger">
                          {{ substr($user->name, 0, 1) }}
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                        <br>
                        <span class="badge bg-{{ $user->estado == 'activo' ? 'success' : 'secondary' }} fs-6">
                          <i class="bx bx-{{ $user->estado == 'activo' ? 'user-check' : 'user-x' }} me-1"></i>
                          {{ ucfirst($user->estado) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Alerta de advertencia -->
            <div class="alert alert-danger border-0 mb-4">
              <div class="d-flex align-items-center">
                <i class="bx bx-exclamation-triangle fs-1 text-danger me-3"></i>
                <div>
                  <h6 class="alert-heading mb-1"><strong>¡ACCIÓN IMPORTANTE!</strong></h6>
                  <p class="mb-0">Esta acción eliminará temporalmente el usuario del sistema. Podrás restaurarlo más tarde desde la sección de eliminados.</p>
                </div>
              </div>
            </div>
            
            <p class="mb-4 fw-bold text-danger">¿Estás seguro que deseas eliminar este usuario?</p>
            
            <!-- Contraseña de administrador -->
            <div class="mb-4">
              <label for="password{{ $user->id }}" class="form-label fw-semibold">
                <i class="bx bx-lock me-1"></i> Contraseña de Administrador
              </label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="bx bx-shield-lock"></i>
                </span>
                <input type="password" 
                       name="password" 
                       id="password{{ $user->id }}"
                       class="form-control" 
                       placeholder="Ingrese su contraseña de administrador" 
                       required
                       autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password{{ $user->id }}')">
                  <i class="bx bx-show"></i>
                </button>
              </div>
              <div class="form-text">
                <i class="bx bx-info-circle me-1"></i>
                Se requiere su contraseña para confirmar esta acción
              </div>
            </div>
            
            <!-- Tipo de observación -->
            <div class="mb-4">
              <label for="tipo_observacion{{ $user->id }}" class="form-label fw-semibold">
                <i class="bx bx-category me-1"></i> Tipo de Observación
              </label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="bx bx-list-ul"></i>
                </span>
                <select name="tipo_observacion" 
                        id="tipo_observacion{{ $user->id }}"
                        class="form-select" 
                        required>
                  <option value="">Seleccione un tipo de observación</option>
                  <option value="inactividad_prolongada">Inactividad Prolongada</option>
                  <option value="solicitud_usuario">Solicitud del Usuario</option>
                  <option value="violacion_politicas">Violación de Políticas</option>
                  <option value="seguridad">Razones de Seguridad</option>
                  <option value="reorganizacion">Reorganización de Personal</option>
                  <option value="finalizacion_contrato">Finalización de Contrato</option>
                  <option value="duplicacion_cuenta">Duplicación de Cuenta</option>
                  <option value="otro">Otro</option>
                </select>
              </div>
              <div class="form-text">
                <i class="bx bx-info-circle me-1"></i>
                Seleccione la categoría que mejor describe el motivo
              </div>
            </div>

            <!-- Checkbox de confirmación -->
            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="confirmacion{{ $user->id }}" required>
                <label class="form-check-label" for="confirmacion{{ $user->id }}">
                  <strong>Confirmo que he leído y entiendo las consecuencias de esta acción</strong>
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="bx bx-x me-1"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-danger" id="btnEliminar{{ $user->id }}" disabled>
              <i class="bx bx-trash me-1"></i> Eliminar Usuario
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

@if($user->deleted_at)
  <!-- Modal Restaurar Usuario -->
  <div class="modal fade" id="modalRestaurarUsuario{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('users.restore', $user->id) }}" method="POST">
          @csrf
          <div class="modal-header bg-success text-white">
            <div class="d-flex align-items-center">
              <i class="bx bx-refresh fs-3 me-2"></i>
              <h5 class="modal-title mb-0">Restaurar Usuario</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-success border-0 mb-4">
              <div class="d-flex align-items-center">
                <i class="bx bx-check-circle fs-1 text-success me-3"></i>
                <div>
                  <h6 class="alert-heading mb-1"><strong>Restaurar Usuario</strong></h6>
                  <p class="mb-0">Esta acción restaurará el usuario y todos sus datos asociados.</p>
                </div>
              </div>
            </div>
            
            <p class="mb-3">¿Estás seguro que deseas restaurar este usuario?</p>
            <p class="text-muted small">El usuario volverá a estar disponible en el sistema con todos sus permisos y datos.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="bx bx-x me-1"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-success">
              <i class="bx bx-refresh me-1"></i> Restaurar Usuario
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Eliminar Definitivamente -->
  <div class="modal fade" id="modalEliminarDefinitivoUsuario{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form action="{{ route('users.forceDelete', $user->id) }}" method="POST">
          @csrf
          <div class="modal-header bg-dark text-white">
            <div class="d-flex align-items-center">
              <i class="bx bx-x-circle fs-3 me-2"></i>
              <h5 class="modal-title mb-0">Eliminar Definitivamente</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-dark border-0 mb-4">
              <div class="d-flex align-items-center">
                <i class="bx bx-error-circle fs-1 text-danger me-3"></i>
                <div>
                  <h6 class="alert-heading mb-1"><strong>¡ACCIÓN IRREVERSIBLE!</strong></h6>
                  <p class="mb-0">Esta acción eliminará permanentemente el usuario y todos sus datos. Esta operación no se puede deshacer.</p>
                </div>
              </div>
            </div>
            
            <p class="mb-3 fw-bold text-danger">¿Estás completamente seguro?</p>
            
            <div class="mb-3">
              <label for="password" class="form-label">Contraseña de Administrador</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bx bx-lock"></i>
                </span>
                <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="motivo" class="form-label">Motivo de la Eliminación Definitiva</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bx bx-message-square"></i>
                </span>
                <textarea name="motivo" class="form-control" rows="3" placeholder="Explique el motivo de la eliminación definitiva" required></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="bx bx-x me-1"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-dark">
              <i class="bx bx-x-circle me-1"></i> Eliminar Definitivamente
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

@endsection

@push('scripts')
<script>
// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  // Eliminar notificaciones visuales y alertas de sesión y errores
});
</script>
@endpush

@push('styles')
<style>
/* Estilos específicos para la vista de detalles */
.avatar-lg {
  width: 4rem;
  height: 4rem;
}

.avatar-initial {
  font-weight: 600;
  font-size: 1.5rem;
}

.card-header {
  border-bottom: 1px solid #e2e8f0;
}

.border {
  border-color: #e2e8f0 !important;
}

.border-primary {
  border-color: #696cff !important;
}

.border-info {
  border-color: #0dcaf0 !important;
}

.border-secondary {
  border-color: #6c757d !important;
}

/* Estilos para las tarjetas de información */
.info-card {
  transition: transform 0.2s ease;
}

.info-card:hover {
  transform: translateY(-2px);
}

/* Estilos para badges */
.badge {
  font-size: 0.75rem;
  padding: 0.5rem 0.75rem;
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
  
  .d-flex.flex-wrap {
    justify-content: center;
  }
  
  .avatar-lg {
    width: 3rem;
    height: 3rem;
  }
  
  .avatar-initial {
    font-size: 1.25rem;
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

/* Estilos específicos para el modal de eliminación */
.bg-light-danger {
  background-color: rgba(220, 53, 69, 0.1) !important;
}

.border-danger {
  border-color: #dc3545 !important;
}

/* Estilos para campos de formulario */
.form-label.fw-semibold {
  font-weight: 600;
  color: #495057;
}

.input-group-text.bg-light {
  background-color: #f8f9fa !important;
  border-color: #dee2e6;
}

/* Estilos para el contador de caracteres */
#contador {
  font-weight: 600;
}

/* Estilos para botones deshabilitados */
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Animaciones para el modal */
.modal-content {
  border: none;
  border-radius: 15px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
  border-radius: 15px 15px 0 0;
}

.modal-footer {
  border-radius: 0 0 15px 15px;
}

/* Estilos para el checkbox de confirmación */
.form-check-input:checked {
  background-color: #dc3545;
  border-color: #dc3545;
}

.form-check-label {
  cursor: pointer;
  user-select: none;
}

/* Estilos para tooltips personalizados */
.custom-tooltip {
  position: absolute;
  background: rgba(0, 0, 0, 0.9);
  color: white;
  padding: 8px 12px;
  border-radius: 6px;
  font-size: 12px;
  z-index: 10000;
  pointer-events: none;
  white-space: nowrap;
  max-width: 250px;
  word-wrap: break-word;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* Responsive para el modal */
@media (max-width: 768px) {
  .modal-dialog {
    margin: 1rem;
  }
  
  .modal-body {
    padding: 1rem;
  }
  
  .input-group {
    flex-direction: column;
  }
  
  .input-group > * {
    margin-bottom: 0.5rem;
  }
}
</style>
@endpush 