@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h1 class="page-title mb-0">
                    <i class="bi bi-person-circle"></i> Mi Perfil
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="bi bi-house me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    {{-- Notificaciones --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('warning') }}</span>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('status') == 'password-updated')
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-shield-check fs-1 text-success"></i>
                <div>
                    <h6 class="alert-heading mb-1"><strong>¡Contraseña Actualizada!</strong></h6>
                    <p class="mb-0">Su contraseña ha sido actualizada exitosamente. Los cambios se han aplicado de inmediato.</p>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Errores de validación de eliminación de cuenta --}}
    @if($errors->userDeletion->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-1 text-danger"></i>
                <div>
                    <h6 class="alert-heading mb-1"><strong>Error al eliminar cuenta</strong></h6>
                    <ul class="mb-0">
                        @foreach($errors->userDeletion->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Estado de la cuenta --}}
    @php $user = Auth::user(); @endphp
    @if($user->deleted_at)
        <div class="alert alert-dark border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-x-circle-fill fs-1 text-dark"></i>
                <div>
                    <h6 class="alert-heading mb-1"><strong>Cuenta Eliminada</strong></h6>
                    <p class="mb-0">Su cuenta ha sido eliminada del sistema. Contacte a soporte si considera que es un error.</p>
                </div>
            </div>
        </div>
    @elseif($user->pending_delete_at)
        @php
            $fechaEliminacion = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(3);
            $ahora = \Carbon\Carbon::now();
            $diferencia = $fechaEliminacion->diff($ahora);
            
            $dias = $diferencia->days;
            $horas = $diferencia->h;
            
            $mensajeTiempo = '';
            if ($dias > 0) {
                $mensajeTiempo = $dias . ' día(s)';
                if ($horas > 0) {
                    $mensajeTiempo .= ' y ' . $horas . ' hora(s)';
                }
            } else {
                $mensajeTiempo = $horas . ' hora(s)';
            }
        @endphp
        <div class="alert alert-warning border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                <div>
                    <h6 class="alert-heading mb-1"><strong>Cuenta en Proceso de Eliminación</strong></h6>
                    <p class="mb-0">Su cuenta se eliminará definitivamente en <strong>{{ $mensajeTiempo }}</strong>.</p>
                    <form action="{{ route('profile.cancelarBorradoCuenta') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Cancelar Eliminación
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($user->estado == 'inactivo')
        <div class="alert alert-secondary border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-person-dash fs-1 text-secondary"></i>
                <div>
                    <h6 class="alert-heading mb-1"><strong>Cuenta Suspendida</strong></h6>
                    <p class="mb-0">Su cuenta ha sido suspendida por un administrador. Contacte soporte para más información.</p>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                <div>
                    <h6 class="alert-heading mb-1"><strong>Cuenta Activa</strong></h6>
                    <p class="mb-0">Su cuenta está funcionando correctamente. Puede gestionar su información y configuración de seguridad.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal motivo suspensión --}}
    @if($user->estado == 'inactivo' && $user->motivo_suspension)
      <div id="modalMotivoSuspension" class="modal fade show" tabindex="-1" style="display:block; background:rgba(0,0,0,0.7);" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow-lg border-0 rounded-4 animate__animated animate__fadeInDown">
            <div class="modal-body text-center p-5">
              <div class="mb-3">
                <i class="bx bx-block fs-1 text-danger"></i>
              </div>
              <h4 class="fw-bold mb-2 text-danger">Cuenta suspendida</h4>
              <p class="mb-2">Tu cuenta ha sido suspendida por el administrador.</p>
              <p class="mb-2"><strong>Motivo:</strong> {{ $user->motivo_suspension }}</p>
              <p class="mb-3">Si crees que fue un error, contacta a soporte:<br>
                <a href="mailto:darwinrvaldiviezo@gmail.com" class="fw-bold text-primary">darwinrvaldiviezo@gmail.com</a>
              </p>
              <button class="btn btn-primary mt-2" onclick="window.location.href='mailto:darwinrvaldiviezo@gmail.com'">
                <i class="bx bx-envelope"></i> Contactar soporte
              </button>
            </div>
          </div>
        </div>
      </div>
      <script>
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', function(e){ e.preventDefault(); });
      </script>
    @endif

    <div class="row">
        {{-- Información del Perfil --}}
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-lg mb-4">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle text-primary me-2"></i> Información Personal
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')
                    @include('profile.partials.update-profile-information-form')
                    </form>
                </div>
            </div>

            {{-- Seguridad --}}
            <div class="card card-outline card-info shadow-lg mb-4">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock text-info me-2"></i> Seguridad
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')
                    @include('profile.partials.update-password-form')
                    </form>
                </div>
            </div>
        </div>

        {{-- Panel lateral --}}
        <div class="col-lg-4">
            {{-- Información de la cuenta --}}
            <div class="card card-outline card-secondary shadow-lg mb-4">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-secondary me-2"></i> Información de Cuenta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small">ID de Usuario</label>
                        <p class="mb-0 fw-bold">{{ $user->id }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Roles</label>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge bg-primary">{{ $role }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Estado</label>
                        <p class="mb-0">
                            @if($user->deleted_at)
                                <span class="badge bg-danger">Eliminado</span>
                            @elseif($user->pending_delete_at)
                                <span class="badge bg-warning text-dark">Pendiente de Eliminación</span>
                            @elseif($user->estado == 'inactivo')
                                <span class="badge bg-secondary">Inactivo</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Miembro desde</label>
                        <p class="mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($user->email_verified_at)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Email verificado</label>
                            <p class="mb-0">{{ $user->email_verified_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    @if($user->pending_delete_at)
                        <div class="mb-3">
                            <label class="form-label text-muted small">Solicitud de eliminación</label>
                            <p class="mb-0">{{ $user->pending_delete_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Eliminar cuenta --}}
            @if(!$user->pending_delete_at && !$user->deleted_at)
            <div class="card card-outline card-danger shadow-lg">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-0">
                        <i class="bi bi-trash text-danger me-2"></i> Eliminar Cuenta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger border-0 shadow-sm mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <strong>Acción irreversible</strong>
                                <p class="mb-0 small">Su cuenta se eliminará en 3 días</p>
                            </div>
                        </div>
                    </div>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            @endif

            @if($user->pending_delete_at)
                <div class="card card-outline card-warning shadow-lg">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h5 class="mb-0">
                            <i class="bi bi-hourglass-split text-warning me-2"></i> Cancelar Eliminación
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning border-0 shadow-sm mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div>
                                    <strong>Su cuenta está en proceso de eliminación.</strong>
                                    <p class="mb-0 small">Puede cancelar la eliminación antes de que se cumplan los 3 días.</p>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('profile.cancelarBorradoCuenta') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Cancelar Eliminación
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Inicializar tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Animaciones para modales
$('.modal').on('show.bs.modal', function () {
    $(this).addClass('animated fadeInDown faster');
});

$('.modal').on('hide.bs.modal', function () {
    $(this).removeClass('animated fadeInDown faster');
});

// Abrir modal de eliminar cuenta si hay errores de validación
@if($errors->userDeletion->any())
  $(document).ready(function() {
    $('#modalEliminarCuentaPerfil').modal('show');
  });
@endif
</script>
@endpush

<style>
/* Estilos profesionales igual que productos/clientes */
.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title i {
    color: #007bff;
    font-size: 2.2rem;
}

.card-outline {
    border-top: 3px solid;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.card-outline.card-primary {
    border-top-color: #007bff;
}

.card-outline.card-info {
    border-top-color: #17a2b8;
}

.card-outline.card-secondary {
    border-top-color: #6c757d;
}

.card-outline.card-danger {
    border-top-color: #dc3545;
}

.card-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-bottom: 2px solid #e9ecef;
    padding: 1.5rem;
    border-radius: 16px 16px 0 0;
}

.card-header h5 {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
    border: 2px solid transparent;
    text-transform: none;
    letter-spacing: 0.3px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease-in-out;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    transform: translateY(-1px);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.alert {
    border-radius: 12px;
    border: none;
}

@media (max-width: 768px) {
    .page-title { font-size: 1.5rem; justify-content: center; }
    .btn-lg { width: 100%; margin-top: 1rem; }
    .card-header, .card-footer { border-radius: 0; }
}
</style>
