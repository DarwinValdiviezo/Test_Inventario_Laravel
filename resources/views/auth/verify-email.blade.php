<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifica tu correo electrónico</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .verify-container {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            padding: 2.5rem 2rem 2rem 2rem;
            margin: 2rem;
        }
        .verify-title {
            font-size: 2rem;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 1rem;
            text-align: center;
        }
        .verify-icon {
            font-size: 3rem;
            color: #4f46e5;
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .btn-link {
            color: #4f46e5;
            text-decoration: underline;
            font-weight: 500;
        }
        .btn-link:hover {
            color: #764ba2;
        }
        .text-muted {
            color: #6c757d !important;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-icon">
            <i class="bi bi-envelope-check-fill"></i>
        </div>
        <div class="verify-title">Verifica tu correo electrónico</div>
        <div class="mb-4 text-muted text-center">
            ¡Gracias por registrarte! Antes de continuar, por favor verifica tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar.
            <br>Si no recibiste el correo, puedes solicitar uno nuevo.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success text-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                Se ha enviado un enlace de verificación a tu correo electrónico.
            </div>
        @endif

        <div class="d-flex flex-column gap-2">
            <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-repeat me-1"></i> Enviar correo de verificación
                </button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>
