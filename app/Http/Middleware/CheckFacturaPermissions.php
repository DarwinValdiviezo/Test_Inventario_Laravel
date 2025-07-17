<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;

class CheckFacturaPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = Auth::user();
        
        // Si es administrador, permitir todo
        if ($user->hasRole('Administrador')) {
            return $next($request);
        }
        
        // Obtener la factura de la ruta
        $facturaId = $request->route('factura');
        if (!$facturaId) {
            return $next($request);
        }
        
        $factura = Factura::find($facturaId);
        if (!$factura) {
            abort(404, 'Factura no encontrada');
        }
        
        // Verificar permisos específicos
        switch ($permission) {
            case 'edit':
                if ($factura->usuario_id !== $user->id) {
                    abort(403, 'Solo el emisor de la factura puede editarla');
                }
                break;
                
            case 'delete':
                if ($factura->usuario_id !== $user->id) {
                    abort(403, 'Solo el emisor de la factura puede anularla');
                }
                break;
                
            case 'restore':
                if ($factura->usuario_id !== $user->id) {
                    abort(403, 'Solo el emisor de la factura puede restaurarla');
                }
                break;
                
            case 'forceDelete':
                // Solo administradores pueden eliminar permanentemente
                abort(403, 'Solo los administradores pueden eliminar permanentemente las facturas');
                break;
        }
        
        return $next($request);
    }
} 