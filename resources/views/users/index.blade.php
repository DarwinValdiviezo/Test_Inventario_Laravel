@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="page-title d-flex flex-column justify-content-center flex-sm-row my-0">
        <div class="page-title-content">
          <h4 class="mb-1">
            <span class="text-muted fw-light">Sistema /</span> Usuarios
          </h4>
          <p class="text-muted mb-0">Gestión completa de usuarios del sistema</p>
        </div>
        <div class="page-title-actions ms-auto">
          @if($filtro != 'eliminados')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
              <i class="bx bx-user-plus me-1"></i> Nuevo Usuario
            </a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Filtros de Estado -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('users.index', array_merge(request()->except('page'), ['filtro' => 'activos'])) }}" 
               class="btn btn-outline-primary {{ $filtro == 'activos' ? 'active' : '' }}">
              <i class="bx bx-user-check me-1"></i> Activos
              <span class="badge bg-primary ms-1">{{ $users->total() }}</span>
            </a>
            <a href="{{ route('users.index', array_merge(request()->except('page'), ['filtro' => 'inactivos'])) }}" 
               class="btn btn-outline-secondary {{ $filtro == 'inactivos' ? 'active' : '' }}">
              <i class="bx bx-user-x me-1"></i> Inactivos
            </a>
            <a href="{{ route('users.index', array_merge(request()->except('page'), ['filtro' => 'pendientes'])) }}" 
               class="btn btn-outline-warning {{ $filtro == 'pendientes' ? 'active' : '' }}">
              <i class="bx bx-time me-1"></i> Pendientes
            </a>
            <a href="{{ route('users.index', array_merge(request()->except('page'), ['filtro' => 'eliminados'])) }}" 
               class="btn btn-outline-danger {{ $filtro == 'eliminados' ? 'active' : '' }}">
              <i class="bx bx-trash me-1"></i> Eliminados
            </a>
                </div>
            </div>
        </div>
    </div>
    </div>

  <!-- Filtros de Búsqueda -->
            @if($filtro != 'eliminados')
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bx bx-search me-1"></i> Filtros de Búsqueda
          </h5>
        </div>
        <div class="card-body">
          <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <input type="hidden" name="filtro" value="{{ $filtro }}">
            <div class="col-md-4">
              <label class="form-label">Buscar</label>
              <input type="text" name="busqueda" value="{{ request('busqueda') }}" 
                     class="form-control" placeholder="Nombre, email, rol...">
                </div>
            <div class="col-md-3">
              <label class="form-label">Rol</label>
                    <select name="rol" class="form-select">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                  <option value="{{ $role->name }}" {{ request('rol') == $role->name ? 'selected' : '' }}>
                    {{ $role->name }}
                  </option>
                        @endforeach
                    </select>
                </div>
            <div class="col-md-2">
              <label class="form-label">Mostrar</label>
                    <select name="cantidad" class="form-select">
                @foreach([5,10,15,20,50] as $n)
                  <option value="{{ $n }}" {{ request('cantidad', 10) == $n ? 'selected' : '' }}>
                    {{ $n }}
                  </option>
                        @endforeach
                    </select>
                </div>
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-search me-1"></i> Buscar
                    </button>
                <a href="{{ route('users.index', ['filtro' => $filtro]) }}" class="btn btn-outline-secondary">
                  <i class="bx bx-x me-1"></i> Limpiar
                </a>
              </div>
            </div>
            </form>
        </div>
      </div>
    </div>
  </div>
            @endif

  <!-- Tabla de Usuarios -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bx bx-group me-1"></i> Lista de Usuarios
            </h5>
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small">
                Mostrando {{ $users->firstItem() ?? 0 }} a {{ $users->lastItem() ?? 0 }} 
                de {{ $users->total() }} registros
              </span>
            </div>
          </div>
        </div>
        
            <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Estado</th>
                <th>Creado</th>
                <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
            <tbody class="table-border-bottom-0">
                        @forelse($users as $user)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
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
                            </td>
                  <td>
                    <span class="fw-semibold">{{ $user->email }}</span>
                  </td>
                            <td>
                                @foreach($user->getRoleNames() as $role)
                      @php
                        $roleColors = [
                          'Administrador' => 'danger',
                          'Secretario' => 'warning',
                          'Ventas' => 'info',
                          'Bodega' => 'success',
                          'Cliente' => 'primary'
                        ];
                        $color = $roleColors[$role] ?? 'secondary';
                      @endphp
                      <span class="badge bg-label-{{ $color }}">{{ $role }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($user->deleted_at)
                      <span class="badge bg-label-danger">
                        <i class="bx bx-trash me-1"></i> Eliminado
                      </span>
                                @elseif($user->pending_delete_at)
                      <span class="badge bg-label-warning">
                        <i class="bx bx-time me-1"></i> Pendiente
                      </span>
                                @elseif($user->estado == 'inactivo')
                      <span class="badge bg-label-secondary">
                        <i class="bx bx-user-x me-1"></i> Inactivo
                      </span>
                                @else
                      <span class="badge bg-label-success">
                        <i class="bx bx-user-check me-1"></i> Activo
                      </span>
                                @endif
                            </td>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="fw-semibold">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                      <small class="text-muted">{{ $user->created_at ? $user->created_at->format('H:i') : '' }}</small>
                    </div>
                  </td>
                            <td class="text-end">
                    <div class="dropdown" data-bs-display="static" data-bs-container="body">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bx bx-cog"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="{{ route('users.show', $user) }}">
                            <i class="bx bx-show me-2"></i> Ver Detalles
                          </a>
                        </li>
                        
                                    @if(!$user->deleted_at && !$user->pending_delete_at)
                          <li>
                            <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                              <i class="bx bx-edit me-2"></i> Editar
                            </a>
                          </li>
                          
                          @if(!$user->getRoleNames()->contains('Administrador'))
                                            @if($user->estado == 'activo')
                              <li>
                                <button class="dropdown-item text-warning" type="button" 
                                        data-bs-toggle="modal" data-bs-target="#modalDesactivarUsuario{{ $user->id }}">
                                  <i class="bx bx-user-x me-2"></i> Desactivar
                                                </button>
                              </li>
                                            @else
                              <li>
                                <button class="dropdown-item text-success" type="button" 
                                        data-bs-toggle="modal" data-bs-target="#modalActivarUsuario{{ $user->id }}">
                                  <i class="bx bx-user-check me-2"></i> Activar
                                                </button>
                              </li>
                                            @endif
                            
                            <li><hr class="dropdown-divider"></li>
                            
                            <li>
                              <button class="dropdown-item text-danger" type="button" 
                                      data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario{{ $user->id }}">
                                <i class="bx bx-trash me-2"></i> Eliminar
                                            </button>
                            </li>
                          @else
                            <li>
                              <span class="dropdown-item text-muted">
                                <i class="bx bx-lock me-2"></i> Protegido
                              </span>
                            </li>
                                        @endif
                                    @elseif($user->pending_delete_at && !$user->deleted_at)
                                        @if(Auth::id() === $user->id)
                            <li>
                              <form action="{{ route('users.cancelarBorradoCuenta', $user) }}" method="POST" style="display:inline;">
                                            @csrf
                                <button type="submit" class="dropdown-item text-success">
                                  <i class="bx bx-undo me-2"></i> Cancelar Eliminación
                                                </button>
                                        </form>
                            </li>
                                        @else
                            <li>
                              <span class="dropdown-item text-muted">
                                <i class="bx bx-x-circle me-2"></i> No Permitido
                              </span>
                            </li>
                                        @endif
                                    @else
                          <li>
                            <button class="dropdown-item text-success" type="button" 
                                    data-bs-toggle="modal" data-bs-target="#modalRestaurarUsuario{{ $user->id }}">
                              <i class="bx bx-refresh me-2"></i> Restaurar
                                        </button>
                          </li>
                          <li>
                            <button class="dropdown-item text-danger" type="button" 
                                    data-bs-toggle="modal" data-bs-target="#modalEliminarDefinitivoUsuario{{ $user->id }}">
                              <i class="bx bx-x-circle me-2"></i> Eliminar Definitivamente
                                        </button>
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
                      <i class="bx bx-user-x bx-lg text-muted mb-3"></i>
                      <h5 class="text-muted">No hay usuarios {{ $filtro == 'eliminados' ? 'eliminados' : 'registrados' }}</h5>
                      <p class="text-muted mb-0">No se encontraron usuarios con los filtros aplicados</p>
                    </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>
        
        <!-- Paginación -->
        @if($users->hasPages())
          <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted">
                Mostrando <b>{{ $users->firstItem() ?? 0 }}</b> a <b>{{ $users->lastItem() ?? 0 }}</b> 
                de <b>{{ $users->total() }}</b> registros
                </div>
                <div>
                    {{ $users->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                </div>
        </div>
    </div>
        @endif
        </div>
            </div>
        </div>
    </div>

<!-- Modales para acciones -->
    @foreach($users as $user)
  {{-- Modal Desactivar Usuario --}}
  @if(!$user->deleted_at && !$user->pending_delete_at && !$user->getRoleNames()->contains('Administrador'))
    <div class="modal fade" id="modalDesactivarUsuario{{ $user->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bx bx-user-x text-warning me-2"></i> Desactivar Usuario
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('users.desactivar', $user) }}" method="POST" id="formDesactivarUsuario{{ $user->id }}">
            @csrf
            <div class="modal-body">
              @if(session('modal') == 'desactivar' && session('user_id') == $user->id && $errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <p>¿Está seguro que desea desactivar al usuario <strong>{{ $user->name }}</strong>?</p>
              <div class="mb-3">
                <label class="form-label">Contraseña de administrador</label>
                <input type="password" name="admin_password" class="form-control" required placeholder="Ingrese su contraseña">
              </div>
              <div class="mb-3">
                <label class="form-label">Motivo de desactivación</label>
                <select name="motivo" class="form-select" required>
                  <option value="">Seleccione un motivo</option>
                  <option value="Inactividad prolongada">Inactividad prolongada</option>
                  <option value="Solicitud del usuario">Solicitud del usuario</option>
                  <option value="Violación de políticas">Violación de políticas</option>
                  <option value="Seguridad">Seguridad</option>
                  <option value="Reorganización de personal">Reorganización de personal</option>
                  <option value="Finalización de contrato">Finalización de contrato</option>
                  <option value="Duplicación de cuenta">Duplicación de cuenta</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="confirm_desactivar" id="confirmDesactivarUsuario{{ $user->id }}" required>
                  <label class="form-check-label" for="confirmDesactivarUsuario{{ $user->id }}">
                    Confirmo que deseo desactivar este usuario
                  </label>
                </div>
              </div>
              <div class="alert alert-warning">
                <i class="bx bx-error-circle me-2"></i>
                <strong>¡Advertencia!</strong> Esta acción desactivará el usuario y no podrá acceder al sistema hasta ser reactivado.
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-warning" id="btnDesactivarUsuario{{ $user->id }}" disabled>
                <i class="bx bx-user-x me-1"></i> Desactivar
              </button>
            </div>
          </form>
        </div>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const form = document.getElementById('formDesactivarUsuario{{ $user->id }}');
          const password = form.querySelector('input[name="admin_password"]');
          const motivo = form.querySelector('select[name="motivo"]');
          const confirm = form.querySelector('input[name="confirm_desactivar"]');
          const btn = document.getElementById('btnDesactivarUsuario{{ $user->id }}');
          function check() {
            btn.disabled = !(password.value && motivo.value && confirm.checked);
          }
          password.addEventListener('input', check);
          motivo.addEventListener('change', check);
          confirm.addEventListener('change', check);
        });
      </script>
    </div>
  @endif

    <!-- Modal Activar Usuario -->
    <div class="modal fade" id="modalActivarUsuario{{ $user->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bx bx-user-check text-success me-2"></i> Activar Usuario
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('users.activar', $user) }}" method="POST" id="formActivarUsuario{{ $user->id }}">
            @csrf
            <div class="modal-body">
              @if(session('modal') == 'activar' && session('user_id') == $user->id && $errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <p>¿Está seguro que desea activar al usuario <strong>{{ $user->name }}</strong>?</p>
              <div class="mb-3">
                <label class="form-label">Contraseña de administrador</label>
                <input type="password" name="password" class="form-control" required placeholder="Ingrese su contraseña">
              </div>
              <div class="mb-3">
                <label class="form-label">Motivo de activación</label>
                <select name="observacion" class="form-select" required>
                  <option value="">Seleccione un motivo</option>
                  <option value="Reincorporación">Reincorporación</option>
                  <option value="Solicitud del usuario">Solicitud del usuario</option>
                  <option value="Corrección de error">Corrección de error</option>
                  <option value="Cambio de estado">Cambio de estado</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <div class="mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="confirm_activar" id="confirmActivarUsuario{{ $user->id }}" required>
                  <label class="form-check-label" for="confirmActivarUsuario{{ $user->id }}">
                    Confirmo que deseo activar este usuario
                  </label>
                </div>
              </div>
              <div class="alert alert-success">
                <i class="bx bx-info-circle me-2"></i>
                <strong>¡Información!</strong> El usuario podrá acceder nuevamente al sistema.
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success" id="btnActivarUsuario{{ $user->id }}" disabled>
                <i class="bx bx-user-check me-1"></i> Activar
              </button>
            </div>
          </form>
        </div>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const form = document.getElementById('formActivarUsuario{{ $user->id }}');
          const password = form.querySelector('input[name="password"]');
          const motivo = form.querySelector('select[name="observacion"]');
          const confirm = form.querySelector('input[name="confirm_activar"]');
          const btn = document.getElementById('btnActivarUsuario{{ $user->id }}');
          function check() {
            btn.disabled = !(password.value && motivo.value && confirm.checked);
          }
          password.addEventListener('input', check);
          motivo.addEventListener('change', check);
          confirm.addEventListener('change', check);
        });
      </script>
    </div>

    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="modalEliminarUsuario{{ $user->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('users.destroy', $user) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            @method('DELETE')
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="bx bx-trash text-danger me-2"></i> Eliminar Usuario
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>¿Está seguro que desea eliminar al usuario <strong>{{ $user->name }}</strong>?</p>
              <div class="mb-3">
                <label class="form-label">Contraseña de administrador</label>
                <input type="password" name="admin_password" class="form-control" required placeholder="Ingrese su contraseña">
              </div>
              <div class="mb-3">
                <label class="form-label">Motivo de eliminación</label>
                <textarea name="motivo" class="form-control" required placeholder="Describa el motivo"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">
                <i class="bx bx-trash me-1"></i> Eliminar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>


  <!-- Modal Restaurar Usuario -->
  @if($user->deleted_at)
    <div class="modal fade" id="modalRestaurarUsuario{{ $user->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bx bx-refresh text-success me-2"></i> Restaurar Usuario
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('users.restore', $user->id) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="modal-body">
              <p>¿Está seguro que desea restaurar al usuario <strong>{{ $user->name }}</strong>?</p>
              <p class="text-muted small">El usuario será reactivado y podrá acceder al sistema.</p>
              <div class="mb-3">
                <label class="form-label">Contraseña de administrador</label>
                <input type="password" name="admin_password" class="form-control" required placeholder="Ingrese su contraseña">
                <div class="invalid-feedback">Ingrese la contraseña de administrador.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Motivo de restauración</label>
                <textarea name="motivo" class="form-control" required placeholder="Describa el motivo"></textarea>
                <div class="invalid-feedback">Ingrese el motivo de la restauración.</div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success">
                <i class="bx bx-refresh me-1"></i> Restaurar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Eliminar Definitivamente -->
    <div class="modal fade" id="modalEliminarDefinitivoUsuario{{ $user->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bx bx-x-circle text-danger me-2"></i> Eliminar Definitivamente
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="{{ route('users.forceDelete', $user->id) }}" method="POST" class="needs-validation" novalidate>
            @csrf
            @method('DELETE')
            <div class="modal-body">
              <div class="alert alert-danger">
                <i class="bx bx-error-circle me-2"></i>
                <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
              </div>
              <p>¿Está seguro que desea eliminar definitivamente al usuario <strong>{{ $user->name }}</strong>?</p>
              <p class="text-muted small">Todos los datos asociados serán eliminados permanentemente.</p>
              <div class="mb-3">
                <label class="form-label">Contraseña de administrador</label>
                <input type="password" name="admin_password" class="form-control" required placeholder="Ingrese su contraseña">
                <div class="invalid-feedback">Ingrese la contraseña de administrador.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Motivo de eliminación permanente</label>
                <textarea name="motivo" class="form-control" required placeholder="Describa el motivo"></textarea>
                <div class="invalid-feedback">Ingrese el motivo de la eliminación permanente.</div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">
                <i class="bx bx-x-circle me-1"></i> Eliminar Definitivamente
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endforeach
@endsection

@push('scripts')
<script>
// Funcionalidades adicionales
document.addEventListener('DOMContentLoaded', function() {
  // Auto-submit de filtros
  const filterInputs = document.querySelectorAll('select[name="rol"], select[name="cantidad"]');
  filterInputs.forEach(input => {
    input.addEventListener('change', function() {
      this.closest('form').submit();
    });
  });

  // Búsqueda en tiempo real
  const searchInput = document.querySelector('input[name="busqueda"]');
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        this.closest('form').submit();
      }, 500);
    });
  }


  // Tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
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
.table th {
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
    font-weight: 600;
  color: #495057;
}

.table td {
  vertical-align: middle;
  border-bottom: 1px solid #f1f3f4;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

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

/* Estilos para filtros */
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

/* Estilos para botones de filtro */
.btn-outline-primary.active {
  background-color: #696cff;
  border-color: #696cff;
  color: white;
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
  }
  
  .btn-group {
    flex-direction: column;
  }
  
  .btn-group .btn {
    border-radius: 0.375rem !important;
    margin-bottom: 0.25rem;
  }
}

/* Asegura que el dropdown siempre esté por encima de la tabla */
.dropdown-menu {
  z-index: 2050 !important;
}

/* Solución para evitar que el menú se recorte en pantallas pequeñas */
@media (max-width: 768px) {
  .dropdown-menu {
    position: static !important;
    transform: none !important;
    width: 100%;
    margin-top: 0.5rem;
    z-index: 2050 !important;
  }
}
</style>
@endpush 