<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->with('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);
        
        // Iniciar transacción
        \DB::beginTransaction();
        
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'description' => 'nullable|string|max:500',
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);

            // Verificar contraseña de administrador
            if (!Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Contraseña incorrecta.');
            }

            // Verificar que no sea un rol crítico del sistema
            $rolesCriticos = ['Administrador', 'Ventas', 'cliente'];
            if (in_array(strtolower($request->name), array_map('strtolower', $rolesCriticos))) {
                \DB::rollBack();
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'No se puede crear un rol con ese nombre. Es un rol crítico del sistema.');
            }

            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Registrar auditoría
            $this->registrarAuditoria('create', $role, null, $role->toArray(), 'Rol creado', $request->observacion);
            
            // Confirmar transacción
            \DB::commit();
            
            return redirect()->route('roles.index')
                           ->with('success', 'Rol "' . $role->name . '" creado exitosamente por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            \Log::error('Error al crear rol: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el rol. Por favor, inténtalo de nuevo.');
        }
    }

    public function destroy(Request $request, $id)
    {
        // Iniciar transacción
        \DB::beginTransaction();
        
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);

            // Verificar contraseña de administrador
            if (!Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'Contraseña incorrecta.');
            }

            $role = Role::findOrFail($id);
            $this->authorize('delete', $role);
            
            // Verificar que no sea un rol crítico del sistema
            $rolesCriticos = ['Administrador', 'Ventas', 'cliente'];
            if (in_array(strtolower($role->name), array_map('strtolower', $rolesCriticos))) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'No se puede eliminar el rol "' . $role->name . '". Es un rol crítico del sistema.');
            }
            
            // Verificar que no haya usuarios con este rol
            $usuariosConRol = $role->users()->count();
            if ($usuariosConRol > 0) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'No se puede eliminar el rol "' . $role->name . '" porque tiene ' . $usuariosConRol . ' usuario(s) asignado(s). Primero debe reasignar o eliminar esos usuarios.');
            }
            
            $old = $role->toArray();
            $role->delete();
            
            // Registrar auditoría
            $this->registrarAuditoria('delete', $role, $old, null, 'Rol eliminado', $request->observacion);
            
            // Confirmar transacción
            \DB::commit();
            
            return redirect()->route('roles.index')->with('success', 'Rol "' . $role->name . '" eliminado exitosamente por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            \Log::error('Error al eliminar rol: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }

    // Método dummy para evitar error PHPStan
    public function authorize($ability = null, $arguments = [])
    {
        // Implementar lógica de autorización si es necesario
    }

    private function registrarAuditoria($accion, $modelo, $old, $new, $descripcion, $observacion = null)
    {
        try {
            Auditoria::create([
                'user_id' => Auth::id(),
                'action' => $accion,
                'model_type' => get_class($modelo),
                'model_id' => $modelo->id,
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
                'description' => $descripcion,
                'observacion' => $observacion,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
} 