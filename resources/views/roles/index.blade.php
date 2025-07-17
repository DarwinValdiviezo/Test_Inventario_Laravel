@extends('layouts.app')

@section('title', 'Gestión de Roles')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema /</span> Roles
          </h4>
          <p class="text-muted mb-0">Gestión completa de roles y permisos del sistema</p>
        </div>
        <div class="page-title-actions ms-auto">
          @can('create', \Spatie\Permission\Models\Role::class)
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
              <i class="bx bx-shield-plus me-1"></i> Nuevo Rol
            </a>
          @endcan
        </div>
      </div>
    </div>
  </div>

  <!-- Estadísticas de Roles -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Total de Roles</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-primary mb-0 me-2">{{ $roles->count() }}</h4>
                <p class="mb-0">roles</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-primary rounded p-2">
                <i class="bx bx-shield bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Roles del Sistema</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-success mb-0 me-2">{{ $roles->whereIn('name', ['Administrador', 'Ventas', 'Cliente'])->count() }}</h4>
                <p class="mb-0">protegidos</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-success rounded p-2">
                <i class="bx bx-shield-check bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Roles Personalizados</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-info mb-0 me-2">{{ $roles->whereNotIn('name', ['Administrador', 'Ventas', 'Cliente'])->count() }}</h4>
                <p class="mb-0">creados</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-info rounded p-2">
                <i class="bx bx-shield-x bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="card-info">
              <p class="card-text">Roles Vacíos</p>
              <div class="d-flex align-items-end mt-2">
                <h4 class="text-warning mb-0 me-2">{{ $roles->where('users_count', 0)->count() }}</h4>
                <p class="mb-0">disponibles</p>
              </div>
            </div>
            <div class="card-icon">
              <span class="badge bg-label-warning rounded p-2">
                <i class="bx bx-user-x bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla de Roles -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bx bx-shield me-1"></i> Lista de Roles
            </h5>
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">
                Mostrando {{ $roles->count() }} roles del sistema
              </span>
            </div>
          </div>
        </div>
        
        <div class="table-responsive">
            <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th>Rol</th>
                <th>Descripción</th>
                <th>Usuarios</th>
                <th>Tipo</th>
                <th>Creado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse($roles as $role)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-3">
                        <div class="avatar-initial rounded-circle bg-label-{{ in_array(strtolower($role->name), ['administrador', 'ventas', 'cliente']) ? 'success' : 'primary' }}">
                          <i class="bx bx-shield"></i>
                        </div>
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $role->name }}</h6>
                        <small class="text-muted">ID: {{ $role->id }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($role->description)
                      <span class="fw-semibold">{{ $role->description }}</span>
                    @else
                      <span class="text-muted fst-italic">Sin descripción</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex flex-column align-items-center">
                      <span class="badge bg-label-{{ $role->users_count > 0 ? 'warning' : 'secondary' }}">
                        {{ $role->users_count }}
                      </span>
                      <small class="text-muted">
                        {{ $role->users_count == 1 ? 'usuario' : 'usuarios' }}
                      </small>
                    </div>
                  </td>
                  <td>
                    @if(in_array(strtolower($role->name), ['administrador', 'ventas', 'cliente']))
                      <span class="badge bg-label-success">
                        <i class="bx bx-shield-check me-1"></i> Sistema
                      </span>
                    @else
                      <span class="badge bg-label-info">
                        <i class="bx bx-shield-x me-1"></i> Personalizado
                      </span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">{{ $role->created_at ? $role->created_at->format('d/m/Y') : '-' }}</span>
                      <small class="text-muted">{{ $role->created_at ? $role->created_at->format('H:i') : '' }}</small>
                    </div>
                  </td>
                  <td class="text-end">
                    <div class="dropdown" data-bs-display="static" data-bs-container="body">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bx bx-cog"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <button class="dropdown-item" type="button" 
                                  data-bs-toggle="modal" data-bs-target="#modalInfoRol{{ $role->id }}">
                            <i class="bx bx-show me-2"></i> Ver Detalles
                          </button>
                        </li>
                        
                        @if(!in_array(strtolower($role->name), ['administrador', 'ventas', 'cliente']) && $role->users_count == 0)
                          @can('delete', $role)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                              <button class="dropdown-item text-danger" type="button" 
                                      data-bs-toggle="modal" data-bs-target="#modalEliminarRol{{ $role->id }}">
                                <i class="bx bx-trash me-2"></i> Eliminar
                              </button>
                            </li>
                          @endcan
                        @else
                          <li>
                            <span class="dropdown-item text-muted">
                              <i class="bx bx-lock me-2"></i> 
                              {{ in_array(strtolower($role->name), ['administrador', 'ventas', 'cliente']) ? 'Protegido' : 'No eliminable' }}
                            </span>
                          </li>
                        @endif
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                      <i class="bx bx-shield-x bx-lg text-muted mb-3"></i>
                      <h5 class="text-muted">No hay roles registrados</h5>
                      <p class="text-muted mb-0">No se encontraron roles en el sistema</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Sistema de Notificaciones -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
  <!-- Las notificaciones se insertarán aquí dinámicamente -->
</div>

<!-- Modales para acciones -->
@foreach($roles as $role)
  <!-- Modal Información del Rol -->
  <div class="modal fade" id="modalInfoRol{{ $role->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bx bx-shield text-primary me-2"></i> Información del Rol
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-muted small">ID del Rol</label>
                <p class="mb-0 fw-bold">{{ $role->id }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted small">Nombre del Rol</label>
                <p class="mb-0 fw-bold">{{ $role->name }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted small">Tipo de Rol</label>
                <p class="mb-0">
                  @if(in_array(strtolower($role->name), ['administrador', 'ventas', 'cliente']))
                    <span class="badge bg-label-success">Rol del Sistema</span>
                  @else
                    <span class="badge bg-label-info">Rol Personalizado</span>
                  @endif
                </p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-muted small">Descripción</label>
                <p class="mb-0">{{ $role->description ?: 'Sin descripción' }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted small">Usuarios Asignados</label>
                <p class="mb-0 fw-bold">{{ $role->users_count }} {{ $role->users_count == 1 ? 'usuario' : 'usuarios' }}</p>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted small">Fecha de Creación</label>
                <p class="mb-0">{{ optional($role->created_at)->format('d/m/Y H:i') }}</p>
              </div>
            </div>
          </div>
          
          @if($role->users_count > 0 && $role->relationLoaded('users'))
            <div class="mt-4">
              <h6 class="text-primary mb-3">
                <i class="bx bx-group me-2"></i> Usuarios con este Rol
              </h6>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Email</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($role->users as $user)
                      <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                          <span class="badge bg-label-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                            {{ $user->email_verified_at ? 'Verificado' : 'Pendiente' }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Eliminar Rol -->
  @if(!in_array(strtolower($role->name), ['administrador', 'ventas', 'cliente']) && $role->users_count == 0)
    @can('delete', $role)
      <div class="modal fade" id="modalEliminarRol{{ $role->id }}" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="bx bx-trash text-danger me-2"></i> Eliminar Rol
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger">
                <i class="bx bx-error-circle me-2"></i>
                <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
              </div>
              
              <p>¿Está seguro que desea eliminar el rol <strong>{{ $role->name }}</strong>?</p>
              
              <div class="card bg-light mb-3">
                <div class="card-body">
                  <h6 class="card-title">Información del Rol</h6>
                  <div class="row">
                    <div class="col-md-6">
                      <small class="text-muted">ID:</small>
                      <p class="mb-1 fw-bold">{{ $role->id }}</p>
                    </div>
                    <div class="col-md-6">
                      <small class="text-muted">Descripción:</small>
                      <p class="mb-1">{{ $role->description ?: 'Sin descripción' }}</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <form action="{{ route('roles.destroy', $role->id) }}" method="POST" id="formEliminarRol{{ $role->id }}">
                @csrf
                @method('DELETE')
                
                <div class="mb-3">
                  <label for="password{{ $role->id }}" class="form-label">Contraseña de Administrador</label>
                  <div class="input-group">
                    <input type="password" name="password" id="password{{ $role->id }}" 
                           class="form-control" placeholder="Tu contraseña actual" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                      <i class="bx bx-hide"></i>
                    </button>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="observacion{{ $role->id }}" class="form-label">Motivo de Eliminación</label>
                  <select name="observacion" id="observacion{{ $role->id }}" class="form-select" required>
                    <option value="">Seleccionar motivo</option>
                    <option value="Rol obsoleto">Rol obsoleto</option>
                    <option value="Reorganización de permisos">Reorganización de permisos</option>
                    <option value="Duplicado en el sistema">Duplicado en el sistema</option>
                    <option value="Problemas de seguridad">Problemas de seguridad</option>
                    <option value="Limpieza de base de datos">Limpieza de base de datos</option>
                    <option value="Error en el sistema">Error en el sistema</option>
                    <option value="Otro">Otro</option>
                  </select>
                </div>
                
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="confirm_delete" id="confirmDelete{{ $role->id }}" required>
                    <label class="form-check-label" for="confirmDelete{{ $role->id }}">
                      Confirmo que deseo eliminar este rol permanentemente
                    </label>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" form="formEliminarRol{{ $role->id }}" class="btn btn-danger" disabled>
                <i class="bx bx-trash me-1"></i> Eliminar Rol
              </button>
            </div>
          </div>
        </div>
      </div>
    @endcan
  @endif
@endforeach
@endsection

@push('scripts')
<script>
// Sistema de notificaciones elegante
class NotificationSystem {
  constructor() {
    this.container = document.getElementById('notification-container');
  }

  show(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
    notification.style.cssText = `
      min-width: 300px;
      max-width: 400px;
      margin-bottom: 10px;
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      animation: slideInRight 0.3s ease-out;
    `;

    const iconMap = {
      success: 'bx-check-circle',
      error: 'bx-error-circle',
      warning: 'bx-warning',
      info: 'bx-info-circle'
    };

    notification.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="bx ${iconMap[type]} fs-4 me-2"></i>
        <div class="flex-grow-1">
          <div class="fw-semibold">${this.getTitle(type)}</div>
          <div class="small">${message}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;

    this.container.appendChild(notification);

    // Auto-remove después del tiempo especificado
    setTimeout(() => {
      this.hide(notification);
    }, duration);

    // Event listener para cerrar manualmente
    notification.querySelector('.btn-close').addEventListener('click', () => {
      this.hide(notification);
    });
  }

  hide(notification) {
    notification.style.animation = 'slideOutRight 0.3s ease-in';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }

  getTitle(type) {
    const titles = {
      success: '¡Éxito!',
      error: 'Error',
      warning: 'Advertencia',
      info: 'Información'
    };
    return titles[type] || 'Notificación';
  }
}

// Inicializar sistema de notificaciones
const notifications = new NotificationSystem();

// Mostrar errores de validación
@if($errors->any())
  @foreach($errors->all() as $error)
    notifications.show('{{ $error }}', 'error');
  @endforeach
@endif

// Funcionalidades adicionales
document.addEventListener('DOMContentLoaded', function() {
  // Validación de formularios de eliminación
  const deleteForms = document.querySelectorAll('form[action*="roles"][method="POST"]');
  deleteForms.forEach(form => {
    const passwordInput = form.querySelector('input[name="password"]');
    const observacionSelect = form.querySelector('select[name="observacion"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const confirmCheckbox = form.querySelector('input[name="confirm_delete"]');

    if (passwordInput && observacionSelect && submitButton) {
      const validateForm = () => {
        const passwordValid = passwordInput.value.trim().length > 0;
        const observacionValid = observacionSelect.value.trim().length > 0;
        const confirmValid = confirmCheckbox ? confirmCheckbox.checked : true;

        if (passwordValid && observacionValid && confirmValid) {
          submitButton.disabled = false;
          submitButton.classList.remove('btn-secondary');
          submitButton.classList.add('btn-danger');
        } else {
          submitButton.disabled = true;
          submitButton.classList.remove('btn-danger');
          submitButton.classList.add('btn-secondary');
        }
      };

      passwordInput.addEventListener('input', validateForm);
      observacionSelect.addEventListener('change', validateForm);
      if (confirmCheckbox) {
        confirmCheckbox.addEventListener('change', validateForm);
      }

      // Validación inicial
      validateForm();
    }
  });

  // Toggle de contraseña
  const togglePasswordButtons = document.querySelectorAll('.toggle-password');
  togglePasswordButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      const input = button.parentNode.querySelector('input[type="password"], input[type="text"]');
      const icon = button.querySelector('i');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
      } else {
        input.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
      }
    });
  });

  // Confirmaciones para acciones críticas
  const criticalActions = document.querySelectorAll('form[action*="destroy"]');
  criticalActions.forEach(form => {
    form.addEventListener('submit', function(e) {
      if (!confirm('¿Está seguro que desea eliminar este rol? Esta acción no se puede deshacer.')) {
        e.preventDefault();
      }
    });
  });

  // Tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
@if(session('success'))
  <script>window.rolesManager?.showNotification(@json(session('success')), 'success');</script>
@endif
@if(session('error'))
  <script>window.rolesManager?.showNotification(@json(session('error')), 'error');</script>
@endif
@if(session('warning'))
  <script>window.rolesManager?.showNotification(@json(session('warning')), 'warning');</script>
@endif
@if(session('info'))
  <script>window.rolesManager?.showNotification(@json(session('info')), 'info');</script>
@endif
@endpush

@push('styles')
<style>
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

/* Estilos para la tabla */




/* Estilos para badges */
.badge {
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
}

/* Estilos para dropdowns */
.dropdown-menu {
  border: none;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  border-radius: 8px;
  z-index: 2050 !important;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

/* Estilos para modales */
.modal-content {
  border: none;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.modal-header {
  border-bottom: 1px solid #f1f3f4;
  padding: 1.5rem;
}

.modal-body {
  padding: 1.5rem;
}

.modal-footer {
  border-top: 1px solid #f1f3f4;
  padding: 1.5rem;
}

/* Estilos para cards */
.card {
  border: none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  border-radius: 12px;
}

.card-header {
  background-color: #fff;
  border-bottom: 1px solid #f1f3f4;
  padding: 1.5rem;
}

.card-body {
  padding: 1.5rem;
}

/* Estilos para estadísticas */
.card-info p {
  color: #6c757d;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.card-icon {
  display: flex;
  align-items: center;
}

/* Responsive */
@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.875rem;
  }
  
  .dropdown-menu {
    position: static !important;
    transform: none !important;
    width: 100%;
    margin-top: 0.5rem;
    z-index: 2050 !important;
  }
  
  .btn-group {
    flex-direction: column;
  }
  
  .btn-group .btn {
    border-radius: 0.375rem !important;
    margin-bottom: 0.25rem;
  }
}
</style>
@endpush 