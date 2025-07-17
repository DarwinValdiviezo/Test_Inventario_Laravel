<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{ url('/') }}" class="brand-link d-flex align-items-center gap-2">
    <img src="{{ asset('vendor/adminlte/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8; width: 35px; height: 35px;">
    <span class="brand-text font-weight-light">Sistema de Facturación</span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Logo y título -->
    {{-- Elimina el logo institucional agregado arriba y deja solo el logo original del monito en el sidebar. --}}
    {{-- Busca y elimina el bloque: --}}
    {{--
    <div class="sidebar-header text-center py-3">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" style="max-width: 48px;">
        <span class="h5 ms-2">Sistema de Facturación</span>
    </div>
    --}}

    <ul class="nav nav-pills flex-column" id="sidebar-menu">
        <!-- Dashboard: visible para todos -->
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="nav-icon bi bi-speedometer2"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <!-- Clientes: solo Administrador y Secretario -->
        @hasanyrole('Administrador|Secretario')
        <li class="nav-item">
            <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->is('clientes*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-people"></i>
                <p>Clientes</p>
            </a>
        </li>
        @endhasanyrole

        <!-- Productos: solo Administrador y Bodega -->
        @hasanyrole('Administrador|Bodega')
        <li class="nav-item">
            <a href="{{ route('productos.index') }}" class="nav-link {{ request()->is('productos*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-box"></i>
                <p>Productos</p>
            </a>
        </li>
        @endhasanyrole

        <!-- Facturación: solo Administrador y Ventas -->
        @hasanyrole('Administrador|Ventas')
        <li class="nav-item">
            <a href="{{ route('facturas.index') }}" class="nav-link {{ request()->is('facturas*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-journal-text"></i>
                <p>Facturación</p>
            </a>
        </li>
        @endhasanyrole

        <!-- Auditoría: solo Administrador -->
        @role('Administrador')
        <li class="nav-item">
            <a href="{{ route('auditorias.index') }}" class="nav-link {{ request()->is('auditorias*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-clipboard-data"></i>
                <p>Auditoría</p>
            </a>
        </li>
        @endrole

        <!-- Gestión de Roles: solo Administrador -->
        @role('Administrador')
        <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-person-gear"></i>
                <p>Gestión de Roles</p>
            </a>
        </li>
        @endrole

        <!-- Usuarios: solo Administrador -->
        @role('Administrador')
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-people-fill"></i>
                <p>Usuarios</p>
            </a>
        </li>
        @endrole
    </ul>
  </div>
  <!-- /.sidebar -->
</aside>
