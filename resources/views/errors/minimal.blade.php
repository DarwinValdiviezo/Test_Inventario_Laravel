<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <style>
            body {
            background: #f8fafc;
            color: #343a40;
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem;
            text-align: center;
            max-width: 420px;
            width: 100%;
        }
        .error-icon {
            font-size: 3.5rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 2.5rem;
            font-weight: 700;
            color: #007bff;
        }
        .error-message {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .btn-main {
            background: #007bff;
            color: #fff;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-main:hover {
            background: #0056b3;
            color: #fff;
        }
        .logo {
            width: 60px;
            margin-bottom: 1rem;
            }
        </style>
    </head>
<body>
    <div class="error-container">
        <img src="{{ asset('vendor/adminlte/img/AdminLTELogo.png') }}" alt="Logo" class="logo">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="error-code">@yield('code')</div>
        <div class="error-message">@yield('message')</div>
        <a href="{{ route('welcome') }}" class="btn btn-main mt-2">
            <i class="bi bi-house-door"></i> Ir al inicio
        </a>
        </div>
    </body>
</html>
