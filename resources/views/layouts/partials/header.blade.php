<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Botón para colapsar el sidebar -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Menú de usuario a la derecha -->
    <ul class="navbar-nav ml-auto align-items-center">
        <!-- Usuario Dropdown -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-toggle="dropdown" style="padding: 0.4rem 1rem;">
                <img src="{{ asset('vendor/adminlte/img/avatar.png') }}" class="user-image img-circle elevation-2" alt="User Image" style="width:36px; height:36px; object-fit:cover;">
                <span class="d-none d-md-inline fw-bold" style="font-size:1rem;">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0 user-dropdown-menu-responsive">
                <!-- Imagen, nombre, email, roles, estado -->
                <li class="user-header bg-gradient-primary text-white p-4 text-center">
                    <img src="{{ asset('vendor/adminlte/img/avatar.png') }}" class="img-circle elevation-2 mb-2" alt="User Image" style="width:70px; height:70px; border:4px solid #fff; box-shadow:0 2px 8px rgba(0,0,0,0.12); object-fit:cover;">
                    <h5 class="mb-1 mt-2 fw-bold text-break">{{ Auth::user()->name }}</h5>
                    <div class="mb-1 small text-break">{{ Auth::user()->email }}</div>
                    <div class="mb-2">
                        @foreach(Auth::user()->getRoleNames() as $role)
                            <span class="badge bg-info text-white small">{{ $role }}</span>
                        @endforeach
                    </div>
                    <div>
                        @php $user = Auth::user(); @endphp
                        @if($user->deleted_at)
                            <span class="badge bg-danger">Eliminado</span>
                        @elseif($user->pending_delete_at)
                            <span class="badge bg-warning text-dark">Pendiente de Eliminación</span>
                        @elseif($user->estado == 'inactivo')
                            <span class="badge bg-secondary">Inactivo</span>
                        @else
                            <span class="badge bg-success">Activo</span>
                        @endif
                    </div>
                </li>
                <!-- Opciones -->
                <li class="user-footer d-flex flex-column flex-md-row justify-content-between gap-2 p-3 bg-light">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm w-100 w-md-50 d-flex align-items-center justify-content-center gap-2 mb-2 mb-md-0">
                        <i class="bi bi-person-circle"></i> Perfil
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-sm w-100 w-md-50 d-flex align-items-center justify-content-center gap-2"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}
.user-header h5 {
    font-weight: 700;
    letter-spacing: 0.5px;
}
.user-header .badge {
    font-size: 0.85rem;
    margin: 0 2px;
    border-radius: 6px;
    padding: 0.35em 0.7em;
}
.user-footer .btn-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1.2rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.2s;
}
.user-footer .btn-lg i {
    font-size: 1.3em;
}
.user-footer .btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.user-dropdown-menu-responsive {
    min-width: 320px;
    max-width: 95vw;
    border-radius: 0.5rem;
    overflow: hidden;
}
@media (max-width: 600px) {
    .user-header {
        padding: 2rem 0.5rem 1.5rem 0.5rem !important;
    }
    .user-header img {
        width: 56px !important;
        height: 56px !important;
    }
    .user-header h5, .user-header .small {
        font-size: 1rem !important;
    }
    .user-footer {
        flex-direction: column !important;
        gap: 0.5rem !important;
        padding: 1rem !important;
    }
    .user-footer .btn-lg {
        width: 100% !important;
        font-size: 1rem !important;
        padding: 0.7rem 1rem !important;
    }
}
.user-footer .btn-sm {
    font-size: 0.98rem;
    padding: 0.55rem 1rem;
    border-radius: 0.45rem;
    font-weight: 600;
    transition: all 0.2s;
}
.user-footer .btn-sm i {
    font-size: 1.1em;
}
</style>
