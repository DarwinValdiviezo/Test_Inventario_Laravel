<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Factura;

class FacturaPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        \Log::info('FacturaPermissions middleware iniciado', [
            'permission' => $permission,
            'route' => $request->route()->getName(),
            'user_id' => auth()->id(),
            'user_roles' => auth()->user()->getRoleNames()->toArray()
        ]);

        $facturaId = $request->route('factura');
        // Si por error llega un modelo, extraer el ID
        if (is_object($facturaId) && isset($facturaId->id)) {
            $facturaId = $facturaId->id;
        }
        
        \Log::info('Factura ID obtenida', ['factura_id' => $facturaId]);
        
        if (!$facturaId) {
            \Log::error('Factura ID no encontrada en la ruta');
            abort(404, 'Factura no encontrada');
        }

        // Obtener la factura desde la base de datos (incluyendo eliminadas)
        $factura = \App\Models\Factura::withTrashed()->find($facturaId);
        
        // Validación extra: si por error se recibe una colección, abortar con mensaje claro
        if ($factura instanceof \Illuminate\Database\Eloquent\Collection) {
            \Log::error('FacturaPermissions: Se recibió una colección en vez de un modelo Factura', [
                'facturaId' => $facturaId,
                'factura' => $factura
            ]);
            abort(500, 'Error interno: Se recibió una colección de facturas en vez de una sola factura. Contacta al administrador.');
        }
        
        \Log::info('Búsqueda de factura', [
            'factura_id' => $facturaId,
            'factura_encontrada' => $factura ? true : false
        ]);
        
        if (!$factura) {
            \Log::error('Factura no encontrada en la base de datos', ['factura_id' => $facturaId]);
            abort(404, 'Factura no encontrada');
        }

        // Verificar permisos según el tipo de acción
        switch ($permission) {
            case 'edit':
                if (!$this->canEdit($factura)) {
                    \Log::error('Usuario no tiene permisos para editar factura', [
                        'user_id' => auth()->id(),
                        'factura_id' => $factura->id
                    ]);
                    abort(403, 'No tienes permisos para editar esta factura');
                }
                break;
                
            case 'delete':
                if (!$this->canDelete($factura)) {
                    \Log::error('Usuario no tiene permisos para anular factura', [
                        'user_id' => auth()->id(),
                        'factura_id' => $factura->id
                    ]);
                    abort(403, 'No tienes permisos para anular esta factura');
                }
                break;
                
            case 'restore':
                if (!$this->canRestore($factura)) {
                    \Log::error('Usuario no tiene permisos para restaurar factura', [
                        'user_id' => auth()->id(),
                        'factura_id' => $factura->id
                    ]);
                    abort(403, 'No tienes permisos para restaurar esta factura');
                }
                break;
                
            case 'forceDelete':
                if (!$this->canForceDelete($factura)) {
                    \Log::error('Usuario no tiene permisos para eliminar permanentemente factura', [
                        'user_id' => auth()->id(),
                        'factura_id' => $factura->id
                    ]);
                    abort(403, 'No tienes permisos para eliminar permanentemente esta factura');
                }
                break;
                
            default:
                \Log::error('Permiso no válido', ['permission' => $permission]);
                abort(403, 'Permiso no válido');
        }

        \Log::info('FacturaPermissions middleware completado exitosamente', [
            'permission' => $permission,
            'factura_id' => $factura->id
        ]);

        return $next($request);
    }

    /**
     * Verificar si el usuario puede editar la factura
     */
    private function canEdit(Factura $factura): bool
    {
        $user = auth()->user();
        
        // Solo el creador o administradores pueden editar
        return $user->hasRole('Administrador') || 
               $user->hasRole('Ventas') || 
               $factura->usuario_id === $user->id;
    }

    /**
     * Verificar si el usuario puede anular la factura
     */
    private function canDelete(Factura $factura): bool
    {
        $user = auth()->user();
        
        // Solo el creador o administradores pueden anular
        return $user->hasRole('Administrador') || 
               $user->hasRole('Ventas') || 
               $factura->usuario_id === $user->id;
    }

    /**
     * Verificar si el usuario puede restaurar la factura
     */
    private function canRestore(Factura $factura): bool
    {
        $user = auth()->user();
        
        // Solo administradores pueden restaurar
        return $user->hasRole('Administrador');
    }

    /**
     * Verificar si el usuario puede eliminar permanentemente la factura
     */
    private function canForceDelete(Factura $factura): bool
    {
        $user = auth()->user();
        
        // Solo administradores pueden eliminar permanentemente
        return $user->hasRole('Administrador');
    }
}
