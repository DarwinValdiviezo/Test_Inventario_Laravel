<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreClienteRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientesController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clientes = Cliente::query();
        if ($request->has('eliminados')) {
            $clientes = $clientes->onlyTrashed();
            
            // Obtener logs de auditoría para clientes eliminados con filtros
            $logs = Auditoria::where('model_type', Cliente::class)
                ->whereIn('action', ['delete', 'restore', 'forceDelete'])
                ->with('user');
            
            // Filtros para la tabla de auditoría
            if ($request->filled('log_buscar')) {
                $logBuscar = $request->input('log_buscar');
                $logs = $logs->where(function($q) use ($logBuscar) {
                    $q->where('description', 'like', "%$logBuscar%")
                      ->orWhere('observacion', 'like', "%$logBuscar%")
                      ->orWhereHas('user', function($userQuery) use ($logBuscar) {
                          $userQuery->where('name', 'like', "%$logBuscar%");
                      });
                });
            }
            
            if ($request->filled('log_accion')) {
                $logs = $logs->where('action', $request->input('log_accion'));
            }
            
            if ($request->filled('log_usuario')) {
                $logs = $logs->whereHas('user', function($q) use ($request) {
                    $q->where('id', $request->input('log_usuario'));
                });
            }
            
            if ($request->filled('log_fecha_desde')) {
                $logs = $logs->whereDate('created_at', '>=', $request->input('log_fecha_desde'));
            }
            
            if ($request->filled('log_fecha_hasta')) {
                $logs = $logs->whereDate('created_at', '<=', $request->input('log_fecha_hasta'));
            }
            
            $logs = $logs->orderBy('created_at', 'desc')
                        ->paginate($request->input('log_per_page', 10))
                        ->withQueryString();
            
            // Obtener usuarios para el filtro
            $usuarios = \App\Models\User::orderBy('name')->get();
        } else {
            $logs = collect(); // Colección vacía si no se solicitan eliminados
            $usuarios = collect();
        }
        
        // Filtros de búsqueda y estado para clientes
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $clientes = $clientes->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('email', 'like', "%$buscar%")
                  ->orWhere('telefono', 'like', "%$buscar%")
                  ->orWhere('direccion', 'like', "%$buscar%") ;
            });
        }
        if ($request->filled('estado')) {
            $clientes = $clientes->where('estado', $request->input('estado'));
        }
        $clientes = $clientes->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('clientes.index', compact('clientes', 'logs', 'usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Cliente::class);
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request)
    {
        try {
            // Iniciar transacción
            \DB::beginTransaction();
            
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            
            // Hashear la contraseña
            $hashedPassword = Hash::make($data['password']);
            
            // Crear cliente primero
            $clienteData = $data;
            $clienteData['password'] = $hashedPassword;
            
            $cliente = Cliente::create($clienteData);
            
            // Crear usuario en la tabla users
            $user = \App\Models\User::create([
                'name' => $data['nombre'],
                'email' => $data['email'],
                'password' => $hashedPassword,
                'email_verified_at' => now(), // Verificar email automáticamente
            ]);
            
            // Asignar rol de cliente al usuario
            $user->assignRole('cliente');
            
            // Actualizar cliente con el user_id
            $cliente->update(['user_id' => $user->id]);
            
            // Registrar auditoría
            $this->registrarAuditoria('create', $cliente, null, $data, 'Cliente creado');
            
            // Confirmar transacción
            \DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente "' . $cliente->nombre . '" creado exitosamente con acceso al sistema.',
                    'redirect' => route('clientes.index')
                ]);
            }
            
            return redirect()->route('clientes.index')
                           ->with('success', 'Cliente "' . $cliente->nombre . '" creado exitosamente con acceso al sistema.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, corrige los errores en el formulario.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput();
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            \Log::error('Error al crear cliente: ' . $e->getMessage());
            
            // Verificar si es un error de email duplicado
            if (strpos($e->getMessage(), 'clientes_email_unique') !== false) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe un cliente con ese correo electrónico.',
                        'errors' => ['email' => ['Ya existe un cliente con ese correo electrónico.']]
                    ], 422);
                }
                
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Ya existe un cliente con ese correo electrónico.');
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el cliente. Por favor, inténtalo de nuevo.',
                    'errors' => ['general' => ['Error al crear el cliente. Por favor, inténtalo de nuevo.']]
                ], 500);
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el cliente. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cliente = \App\Models\Cliente::with(['facturas'])->withTrashed()->findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $this->authorize('update', $cliente);
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreClienteRequest $request, $id)
    {
        // Iniciar transacción
        \DB::beginTransaction();
        
        try {
            $cliente = Cliente::withTrashed()->findOrFail($id);
            $this->authorize('update', $cliente);
            
            $old = $cliente->toArray();
            $data = $request->validated();
            
            // Verificar si hay cambios reales
            $hasChanges = false;
            $fieldsToCheck = ['nombre', 'email', 'telefono', 'direccion', 'estado'];
            
            foreach ($fieldsToCheck as $field) {
                if (isset($data[$field]) && $data[$field] !== $cliente->$field) {
                    $hasChanges = true;
                    break;
                }
            }
            
            // Verificar si se proporcionó una nueva contraseña
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
                $hasChanges = true;
                
                // Actualizar también la contraseña en la tabla users si existe
                if ($cliente->user_id) {
                    $user = \App\Models\User::find($cliente->user_id);
                    if ($user) {
                        $user->update(['password' => $data['password']]);
                    }
                }
            } else {
                unset($data['password']); // No actualizar la contraseña si no se proporciona
            }
            
            // Si no hay cambios, retornar sin actualizar
            if (!$hasChanges) {
                \DB::rollBack();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se realizaron cambios en el cliente "' . $cliente->nombre . '".'
                    ], 422);
                }
                
                return redirect()->route('clientes.show', $cliente)
                               ->with('info', 'No se realizaron cambios en el cliente "' . $cliente->nombre . '".');
            }
            
            $data['updated_by'] = auth()->id();
            $cliente->update($data);
            
            // Sincronizar estado con User si existe y tiene rol cliente
            if ($cliente->user_id) {
                $user = \App\Models\User::find($cliente->user_id);
                if ($user && $user->hasRole('cliente')) {
                    $user->estado = $cliente->estado;
                    $user->save();
                }
            }
            
            // Registrar auditoría
            $this->registrarAuditoria('update', $cliente, $old, $data, 'Cliente actualizado');
            
            // Confirmar transacción
            \DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cliente "' . $cliente->nombre . '" actualizado exitosamente.',
                    'redirect' => route('clientes.show', $cliente)
                ]);
            }
            
            return redirect()->route('clientes.show', $cliente)
                           ->with('success', 'Cliente "' . $cliente->nombre . '" actualizado exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor, corrige los errores en el formulario.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput();
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            \Log::error('Error al actualizar cliente: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el cliente. Por favor, inténtalo de nuevo.',
                    'errors' => ['general' => ['Error al actualizar el cliente. Por favor, inténtalo de nuevo.']]
                ], 500);
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el cliente. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Cliente $cliente)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'observacion' => 'required|string',
        ], [
            'password.required' => 'La contraseña es requerida.',
            'observacion.required' => 'La observación es requerida.',
        ]);

        if ($validator->fails()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput()
                           ->with('modal', 'eliminar-'.$cliente->id);
        }

        // Iniciar transacción
        \DB::beginTransaction();
        
        try {
            // Log de depuración para ver qué datos llegan
            \Log::info('Datos recibidos en destroy:', [
                'cliente_id' => $cliente->id,
                'password_provided' => !empty($request->password),
                'observacion_provided' => !empty($request->observacion),
                'observacion_length' => strlen($request->observacion ?? ''),
                'all_data' => $request->all()
            ]);

            // Verificar contraseña de administrador
            if (!Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                if (request()->expectsJson()) {
                    return response()->json([
                        'errors' => ['password' => ['Contraseña incorrecta.']]
                    ], 422);
                }
                return redirect()->back()
                               ->withErrors(['password' => 'Contraseña incorrecta.'])
                               ->withInput()
                               ->with('modal', 'eliminar-'.$cliente->id);
            }

            $this->authorize('delete', $cliente);
            $old = $cliente->toArray();
            
            // Verificar que el cliente no esté ya eliminado
            if ($cliente->trashed()) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'El cliente ya está eliminado.');
            }
            
            \Log::info('Intentando eliminar cliente:', ['cliente_id' => $cliente->id, 'nombre' => $cliente->nombre]);
            
            // Intentar el soft delete directamente, ya que Cliente siempre es un modelo Eloquent
            $result = $cliente->delete();
            
            \Log::info('Resultado del delete:', ['result' => $result, 'cliente_id' => $cliente->id]);
            
            if (!$result) {
                throw new \Exception('No se pudo eliminar el cliente');
            }
            
            // Sincronizar borrado con User si existe y tiene rol cliente
            if ($cliente->user_id) {
                $user = \App\Models\User::find($cliente->user_id);
                if ($user && $user->hasRole('cliente')) {
                    $user->delete();
                }
            }
            
            // Registrar auditoría
            $this->registrarAuditoria('delete', $cliente, $old, null, 'Cliente eliminado (soft)', $request->observacion);
            
            // Confirmar transacción
            \DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'El cliente "' . $cliente->nombre . '" fue eliminado temporalmente por ' . auth()->user()->name . '.'
                ]);
            }
            return redirect()->route('clientes.index')->with('success', 'El cliente "' . $cliente->nombre . '" fue eliminado temporalmente por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            \Log::error('Error al eliminar cliente: ' . $e->getMessage(), [
                'cliente_id' => $cliente->id ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }



    public function restore(Request $request, $id)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'observacion' => 'required|string',
        ], [
            'password.required' => 'La contraseña es requerida.',
            'observacion.required' => 'La observación es requerida.',
        ]);

        if ($validator->fails()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput()
                           ->with('modal', 'restaurar-'.$id);
        }

        // Iniciar transacción
        \DB::beginTransaction();
        
        try {
            // Verificar contraseña de administrador
            if (!Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                if (request()->expectsJson()) {
                    return response()->json([
                        'errors' => ['password' => ['Contraseña incorrecta.']]
                    ], 422);
                }
                return redirect()->back()
                               ->withErrors(['password' => 'Contraseña incorrecta.'])
                               ->withInput()
                               ->with('modal', 'restaurar-'.$id);
            }

            // Buscar el cliente eliminado
            $cliente = Cliente::onlyTrashed()->findOrFail($id);
            
            // Verificar que el cliente esté eliminado
            if (!$cliente->trashed()) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'El cliente no está eliminado.');
            }

            $this->authorize('restore', $cliente);
            
            $cliente->restore();
            
            // Sincronizar restauración con User si existe y tiene rol cliente
            if ($cliente->user_id) {
                $user = \App\Models\User::withTrashed()->find($cliente->user_id);
                if ($user && $user->hasRole('cliente')) {
                    $user->restore();
                }
            }
            
            // Registrar auditoría
            $this->registrarAuditoria('restore', $cliente, null, $cliente->toArray(), 'Cliente restaurado', $request->observacion);
            
            // Confirmar transacción
            \DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'El cliente "' . $cliente->nombre . '" fue restaurado por ' . auth()->user()->name . '.'
                ]);
            }
            return redirect()->route('clientes.index', ['eliminados' => 1])->with('success', 'El cliente "' . $cliente->nombre . '" fue restaurado por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            \Log::error('Error al restaurar cliente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al restaurar el cliente: ' . $e->getMessage());
        }
    }

    public function forceDelete(Request $request, $id)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'observacion' => 'required|string',
        ], [
            'password.required' => 'La contraseña es requerida.',
            'observacion.required' => 'La observación es requerida.',
        ]);

        if ($validator->fails()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput()
                           ->with('modal', 'borrar-definitivo-'.$id);
        }

        // Iniciar transacción
        \DB::beginTransaction();
        
        try {
            // Verificar contraseña de administrador
            if (!Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                if (request()->expectsJson()) {
                    return response()->json([
                        'errors' => ['password' => ['Contraseña incorrecta.']]
                    ], 422);
                }
                return redirect()->back()
                               ->withErrors(['password' => 'Contraseña incorrecta.'])
                               ->withInput()
                               ->with('modal', 'borrar-definitivo-'.$id);
            }

            // Buscar el cliente eliminado
            $cliente = Cliente::onlyTrashed()->findOrFail($id);
            
            // Verificar que el cliente esté eliminado
            if (!$cliente->trashed()) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'El cliente no está eliminado.');
            }

            $this->authorize('forceDelete', $cliente);
            
            $old = $cliente->toArray();
            $cliente->forceDelete();
            
            // Sincronizar borrado definitivo con User si existe y tiene rol cliente
            if ($cliente->user_id) {
                $user = \App\Models\User::withTrashed()->find($cliente->user_id);
                if ($user && $user->hasRole('cliente')) {
                    $user->forceDelete();
                }
            }
            
            // Registrar auditoría
            $this->registrarAuditoria('forceDelete', $cliente, $old, null, 'Cliente eliminado permanentemente', $request->observacion);
            
            // Confirmar transacción
            \DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'El cliente "' . $cliente->nombre . '" fue eliminado permanentemente por ' . auth()->user()->name . '.'
                ]);
            }
            return redirect()->route('clientes.index', ['eliminados' => 1])->with('success', 'El cliente "' . $cliente->nombre . '" fue eliminado permanentemente por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            \DB::rollBack();
            
            \Log::error('Error al eliminar permanentemente cliente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar permanentemente el cliente: ' . $e->getMessage());
        }
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
