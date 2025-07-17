<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Expirada - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .session-expired-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .warning-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .title {
            color: #dc3545;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .message {
            font-size: 1.25rem;
            color: #495057;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .countdown {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 1rem 2rem;
            border-radius: 15px;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            box-shadow: 0 8px 16px rgba(220, 53, 69, 0.3);
            animation: bounce 1s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .countdown-number {
            font-size: 2rem;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .login-button {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .footer-text {
            margin-top: 2rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .session-expired-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .title {
                font-size: 2rem;
            }
            
            .message {
                font-size: 1.1rem;
            }
            
            .countdown {
                font-size: 1.25rem;
                padding: 0.75rem 1.5rem;
            }
            
            .countdown-number {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="session-expired-card">
        <div class="warning-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        
        <h1 class="title">¡Sesión Interrumpida!</h1>
        
        <div class="message">
            {!! nl2br(e($error)) !!}
        </div>
        
        <div class="countdown">
            <div>Redirigiendo al login en</div>
            <div class="countdown-number">
                <span id="countdown">5</span> segundos
            </div>
        </div>
        
        <a href="{{ route('login') }}" class="login-button">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Ir al Login
        </a>
        
        <div class="footer-text">
            <i class="bi bi-shield-check me-1"></i>
            Sistema de Inventario - Seguridad Activada
        </div>
    </div>

    <script>
        let count = 5;
        const countdown = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            count--;
            countdown.textContent = count;
            
            if (count <= 0) {
                clearInterval(interval);
                window.location.href = '{{ route("login") }}';
            }
        }, 1000);
        
        // Efecto de escritura para el mensaje
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.querySelector('.message');
            const text = message.innerHTML;
            message.innerHTML = '';
            
            let i = 0;
            const typeWriter = () => {
                if (i < text.length) {
                    message.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, 50);
                }
            };
            
            setTimeout(typeWriter, 500);
        });
    </script>
</body>
</html> 