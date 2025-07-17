<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $clienteId = null;
        
        // Obtener el ID del cliente de diferentes formas según la ruta
        if ($this->route('cliente') && is_object($this->route('cliente'))) {
            $clienteId = $this->route('cliente')->id;
        } elseif ($this->route('cliente') && is_numeric($this->route('cliente'))) {
            $clienteId = $this->route('cliente');
        } elseif ($this->route('id')) {
            $clienteId = $this->route('id');
        }
        
        // Reglas diferentes para crear vs actualizar
        if ($this->isMethod('POST')) {
            return [
                'nombre' => 'required|string|max:100',
                'email' => 'required|email|unique:clientes,email',
                'password' => 'required|min:6',
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string|max:255',
                'estado' => 'required|in:activo,inactivo',
            ];
        } else {
            return [
                'nombre' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('clientes', 'email')->ignore($clienteId)
                ],
                'password' => 'nullable|min:6',
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string|max:255',
                'estado' => 'required|in:activo,inactivo',
            ];
        }
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'direccion.string' => 'La dirección debe ser texto.',
            'direccion.max' => 'La dirección no puede tener más de 255 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ];
    }

    public function attributes()
    {
        return [
            'nombre' => 'nombre del cliente',
            'email' => 'correo electrónico',
        ];
    }
}
