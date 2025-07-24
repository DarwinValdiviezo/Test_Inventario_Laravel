<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\FacturaApiController;
use App\Http\Controllers\Api\AuditoriaApiController;
use App\Http\Controllers\Api\RoleApiController;

// Rutas públicas
Route::post('/auth/login', [AuthApiController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/me', [AuthApiController::class, 'me']);
    Route::post('/auth/cambiar-password', [AuthApiController::class, 'cambiarPassword']);

    // Usuarios (solo admin)
    Route::middleware('role:Administrador')->group(function () {
        Route::apiResource('users', UserApiController::class);
        Route::post('/users/{user}/toggle-estado', [UserApiController::class, 'toggleEstado']);
        Route::post('/users/{user}/activar', [UserApiController::class, 'activarUsuario']);
        Route::post('/users/{user}/desactivar', [UserApiController::class, 'desactivarUsuario']);
        Route::post('/users/{id}/restore', [UserApiController::class, 'restore']);
        Route::delete('/users/{id}/force-delete', [UserApiController::class, 'forceDelete']);
        Route::post('/users/crear-token', [UserApiController::class, 'crearTokenAcceso']);
    });

    // Clientes (admin y secretario)
    Route::middleware('role:Administrador|Secretario')->group(function () {
        Route::apiResource('clientes', ClienteApiController::class);
        Route::post('/clientes/{id}/restore', [ClienteApiController::class, 'restore']);
        Route::delete('/clientes/{id}/force-delete', [ClienteApiController::class, 'forceDelete']);
    });

    // Productos (admin y bodega)
    Route::middleware('role:Administrador|Bodega')->group(function () {
        Route::apiResource('productos', ProductoApiController::class);
        Route::post('/productos/{id}/restore', [ProductoApiController::class, 'restore']);
        Route::post('/productos/{id}/forceDelete', [ProductoApiController::class, 'forceDelete']);
        Route::get('productos/export/{type}', [ProductoApiController::class, 'export']);
        Route::get('productos/reporte', [ProductoApiController::class, 'reporte']);
    });

    // Facturas (admin y ventas)
    Route::middleware('role:Administrador|Ventas')->group(function () {
        Route::apiResource('facturas', FacturaApiController::class);
        Route::get('/facturas/{factura}/pdf', [FacturaApiController::class, 'downloadPDF']);
        Route::post('/facturas/{factura}/send-email', [FacturaApiController::class, 'sendEmail']);
        Route::post('/facturas/{factura}/firmar', [FacturaApiController::class, 'firmar']);
        Route::post('/facturas/{factura}/emitir', [FacturaApiController::class, 'emitir']);
        Route::post('/facturas/{factura}/restore', [FacturaApiController::class, 'restore']);
        Route::post('/facturas/{factura}/force-delete', [FacturaApiController::class, 'forceDelete']);
        Route::get('/facturas/{factura}/estado', [FacturaApiController::class, 'estado']);
        Route::get('/facturas/estadisticas', [FacturaApiController::class, 'estadisticas']);
    });

    // Auditoría (solo admin)
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/auditorias', [AuditoriaApiController::class, 'index']);
        Route::get('/auditorias/export', [AuditoriaApiController::class, 'export']);
    });

    // Roles (solo admin)
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/roles', [RoleApiController::class, 'index']);
        Route::post('/roles', [RoleApiController::class, 'store']);
        Route::delete('/roles/{id}', [RoleApiController::class, 'destroy']);
    });

    // Categorías y estadísticas generales
    Route::get('/categorias', [ProductoApiController::class, 'categorias']);
    Route::get('/productos/estadisticas', [ProductoApiController::class, 'estadisticas']);
    Route::get('/clientes', [FacturaApiController::class, 'clientes']);
});