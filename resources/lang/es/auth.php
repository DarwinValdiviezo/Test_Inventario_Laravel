<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Estas credenciales no coinciden con nuestros registros.',
    'password_error' => 'La contraseña proporcionada es incorrecta.',
    'throttle' => 'Demasiados intentos de acceso. Por favor intente nuevamente en :seconds segundos.',

    /*
    |--------------------------------------------------------------------------
    | Custom Authentication Messages
    |--------------------------------------------------------------------------
    |
    | Custom messages for authentication related actions.
    |
    */

    'login' => [
        'success' => '¡Bienvenido! Has iniciado sesión correctamente.',
        'failed' => 'Las credenciales proporcionadas son incorrectas.',
        'inactive' => 'Tu cuenta está inactiva. Contacta al administrador.',
        'suspended' => 'Tu cuenta ha sido suspendida. Contacta al administrador.',
        'deleted' => 'Tu cuenta ha sido eliminada. Contacta al administrador.',
        'session_expired' => 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
        'too_many_attempts' => 'Demasiados intentos fallidos. Tu cuenta ha sido bloqueada temporalmente.',
    ],

    'register' => [
        'success' => '¡Cuenta creada exitosamente! Ya puedes iniciar sesión.',
        'failed' => 'No se pudo crear la cuenta. Por favor, intenta nuevamente.',
        'email_exists' => 'El correo electrónico ya está registrado en el sistema.',
        'username_exists' => 'El nombre de usuario ya está en uso.',
    ],

    'logout' => [
        'success' => 'Has cerrado sesión correctamente.',
        'failed' => 'Error al cerrar sesión.',
    ],

    'password' => [
        'reset' => [
            'success' => 'Tu contraseña ha sido restablecida correctamente.',
            'failed' => 'No se pudo restablecer la contraseña.',
            'token_invalid' => 'El token de restablecimiento de contraseña es inválido.',
            'token_expired' => 'El token de restablecimiento de contraseña ha expirado.',
        ],
        'email' => [
            'sent' => 'Te hemos enviado un enlace para restablecer tu contraseña.',
            'failed' => 'No se pudo enviar el email de restablecimiento.',
        ],
        'change' => [
            'success' => 'Tu contraseña ha sido cambiada correctamente.',
            'failed' => 'No se pudo cambiar la contraseña.',
            'current_incorrect' => 'La contraseña actual es incorrecta.',
        ],
    ],

    'verification' => [
        'sent' => 'Te hemos enviado un enlace de verificación.',
        'verified' => 'Tu correo electrónico ha sido verificado correctamente.',
        'already_verified' => 'Tu correo electrónico ya ha sido verificado.',
        'failed' => 'No se pudo verificar tu correo electrónico.',
    ],

    'two_factor' => [
        'enabled' => 'La autenticación de dos factores ha sido habilitada.',
        'disabled' => 'La autenticación de dos factores ha sido deshabilitada.',
        'code_invalid' => 'El código de autenticación es inválido.',
        'code_expired' => 'El código de autenticación ha expirado.',
        'backup_codes_generated' => 'Se han generado nuevos códigos de respaldo.',
        'backup_codes_used' => 'Se ha usado un código de respaldo.',
    ],

    'session' => [
        'expired' => 'Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente.',
        'concurrent' => 'Se ha detectado otra sesión activa. Tu sesión actual ha sido cerrada.',
        'security' => 'Se ha detectado actividad sospechosa. Tu sesión ha sido cerrada por seguridad.',
    ],

    'account' => [
        'locked' => 'Tu cuenta ha sido bloqueada por múltiples intentos fallidos.',
        'unlocked' => 'Tu cuenta ha sido desbloqueada.',
        'suspended' => 'Tu cuenta ha sido suspendida por el administrador.',
        'reactivated' => 'Tu cuenta ha sido reactivada.',
        'deleted' => 'Tu cuenta ha sido eliminada.',
        'restored' => 'Tu cuenta ha sido restaurada.',
    ],

    'permissions' => [
        'insufficient' => 'No tienes permisos suficientes para realizar esta acción.',
        'denied' => 'Acceso denegado. No tienes autorización para acceder a este recurso.',
        'role_required' => 'Se requiere un rol específico para realizar esta acción.',
        'admin_required' => 'Se requieren permisos de administrador para realizar esta acción.',
    ],

    'security' => [
        'ip_blocked' => 'Tu dirección IP ha sido bloqueada por seguridad.',
        'device_blocked' => 'Tu dispositivo ha sido bloqueado por seguridad.',
        'location_blocked' => 'El acceso desde tu ubicación ha sido bloqueado.',
        'suspicious_activity' => 'Se ha detectado actividad sospechosa en tu cuenta.',
    ],

]; 