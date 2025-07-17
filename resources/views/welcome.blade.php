<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido | Inventario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
        }
        .navbar {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .hero {
            background: #fff;
            padding: 80px 0 60px 0;
            text-align: center;
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 700;
            color: #1a237e;
        }
        .hero-subtitle {
            font-size: 1.3rem;
            color: #495057;
            margin-bottom: 32px;
        }
        .features {
            padding: 60px 0 40px 0;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #1976d2;
            margin-bottom: 16px;
        }
        .feature-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .cta-section {
            background: #1976d2;
            color: #fff;
            padding: 50px 0;
            text-align: center;
        }
        .footer {
            background: #222;
            color: #bbb;
            padding: 30px 0 10px 0;
            text-align: center;
        }
        .footer a { color: #90caf9; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="fw-bold text-primary">Inventario</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Características</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Registrarse</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="hero-title mb-3">Bienvenido al Sistema de Facturación</h1>
            <p class="hero-subtitle mb-4">Gestiona tus productos, usuarios y facturación de manera eficiente, segura y moderna.<br>Optimiza tu negocio con nuestra plataforma profesional.</p>
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 me-2">Comenzar ahora</a>
            <a href="#features" class="btn btn-outline-primary btn-lg px-4">Ver características</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="row text-center mb-4">
                <h2 class="fw-bold text-primary">Características principales</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="feature-icon mb-2"><i class='bx bx-package'></i></div>
                        <div class="feature-title mb-2">Gestión de Productos</div>
                        <div>Control total de inventario, stock y movimientos de productos.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="feature-icon mb-2"><i class='bx bx-user'></i></div>
                        <div class="feature-title mb-2">Gestión de Usuarios</div>
                        <div>Roles, permisos y administración avanzada de usuarios.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="feature-icon mb-2"><i class='bx bx-file'></i></div>
                        <div class="feature-title mb-2">Facturación Electrónica</div>
                        <div>Emisión y control de facturas con reportes detallados.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action / Contacto -->
    <section class="cta-section" id="contacto">
        <div class="container">
            <h2 class="mb-3">¿Listo para optimizar tu gestión?</h2>
            <p class="mb-4">Contáctanos para más información o comienza a usar el sistema ahora mismo.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 me-2">Crear cuenta</a>
            <a href="mailto:soporte@tusistema.com" class="btn btn-outline-light btn-lg px-4">Contactar soporte</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="mb-1">&copy; {{ date('Y') }} Inventario. Todos los derechos reservados.</p>
                    <small>Desarrollado con <i class='bx bxs-heart text-danger'></i> por tu equipo.</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
