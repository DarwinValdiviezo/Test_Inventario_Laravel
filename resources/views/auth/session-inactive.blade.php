<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Suspendida</title>
    <meta http-equiv="refresh" content="5;url={{ route('login') }}">
    <style>
        body {
            background: #f4f6f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .suspendido-box {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 400px;
            text-align: center;
        }
        .suspendido-box .icon {
            font-size: 3.5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .suspendido-box h2 {
            color: #dc3545;
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
        }
        .suspendido-box p {
            color: #333;
            font-size: 1.08rem;
            margin-bottom: 1.2rem;
        }
        .suspendido-box .contador {
            display: inline-block;
            background: #f8d7da;
            color: #721c24;
            font-weight: bold;
            border-radius: 8px;
            padding: 0.4rem 1.2rem;
            font-size: 1.2rem;
            margin-bottom: 1.2rem;
        }
        .suspendido-box .contacto {
            font-size: 0.98rem;
            color: #555;
            margin-top: 1.2rem;
        }
        .suspendido-box .contacto a {
            color: #007bff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div id="overlay-suspendido">
        <div class="suspendido-box">
            <div class="icon">&#9888;</div>
            <h2>Cuenta Suspendida</h2>
            <p>Tu cuenta ha sido suspendida y no puedes acceder al sistema.</p>
            <div class="contador">Redirigiendo al login en <span id="segundos">5</span> segundos...</div>
            <div class="contacto">
                Si crees que esto es un error, contacta a administración:<br>
                <a href="mailto:darwinrvaldiviezo@gmail.com">darwinrvaldiviezo@gmail.com</a>
            </div>
        </div>
    </div>
    <style>
    #overlay-suspendido {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(30, 30, 30, 0.75);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .suspendido-box {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        padding: 2.5rem 2rem 2rem 2rem;
        max-width: 400px;
        text-align: center;
        animation: fadeIn .4s;
    }
    .suspendido-box .icon {
        font-size: 3.5rem;
        color: #dc3545;
        margin-bottom: 1rem;
    }
    .suspendido-box h2 {
        color: #dc3545;
        font-size: 1.6rem;
        margin-bottom: 0.5rem;
    }
    .suspendido-box p {
        color: #333;
        font-size: 1.08rem;
        margin-bottom: 1.2rem;
    }
    .suspendido-box .contador {
        display: inline-block;
        background: #f8d7da;
        color: #721c24;
        font-weight: bold;
        border-radius: 8px;
        padding: 0.4rem 1.2rem;
        font-size: 1.2rem;
        margin-bottom: 1.2rem;
    }
    .suspendido-box .contacto {
        font-size: 0.98rem;
        color: #555;
        margin-top: 1.2rem;
    }
    .suspendido-box .contacto a {
        color: #007bff;
        text-decoration: underline;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    </style>
    <script>
    // Contador y redirección forzada
    let s = 5;
    const el = document.getElementById('segundos');
    const url = '{{ route('login') }}';
    const interval = setInterval(() => {
        if (s > 1) {
            s--;
            el.textContent = s;
        } else {
            clearInterval(interval);
            window.location.href = url;
        }
    }, 1000);
    </script>
</body>
</html> 