<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductoRequest extends FormRequest
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
        $productoId = null;
        
        // Obtener el ID del producto si estamos editando
        if ($this->isMethod('PUT')) {
            // Obtener el ID del parámetro de la ruta
            $productoId = $this->route('producto');
            if (is_object($productoId)) {
                $productoId = $productoId->id;
            } elseif (is_numeric($productoId)) {
                $productoId = (int) $productoId;
            } else {
                $productoId = null;
            }
        }
        
        $rules = [
            'nombre' => [
                'required',
                'string',
                'max:100',
                function($attribute, $value, $fail) use ($productoId) {
                    $categoriaId = $this->input('categoria_id');
                    $query = \App\Models\Producto::whereRaw('LOWER(nombre) = ?', [mb_strtolower($value, 'UTF8')]);
                    if ($categoriaId) {
                        $query->where('categoria_id', $categoriaId);
                    }
                    // Si estamos editando, excluir el producto actual
                    if ($productoId) {
                        $query->where('id', '!=', $productoId);
                    }
                    $exists = $query->exists();
                    if ($exists) {
                        $fail('Ya existe un producto con ese nombre en esta categoría. Elige un nombre diferente o cambia la categoría.');
                    }
                },
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
                'not_regex:/<|>|<script|<\/script/i',
            ],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000'],
            'precio' => ['required', 'numeric', 'gt:0', 'max:10000'],
        ];
        
        // Imagen obligatoria solo en creación
        if ($this->isMethod('POST')) {
            $rules['imagen'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        } else {
            $rules['imagen'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }
        
        return $rules;
    }

    public function messages()
    {
        $messages = [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'nombre.unique' => 'Ya existe un producto con ese nombre.',
            'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'descripcion.not_regex' => 'La descripción no puede contener etiquetas <, > ni scripts.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser menor que 0.',
            'stock.max' => 'El stock no puede ser mayor a 1000 unidades por día.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.gt' => 'El precio debe ser mayor a 0.',
            'precio.max' => 'El precio no puede ser mayor a $10,000.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
        ];
        
        // Mensajes específicos según si es creación o edición
        if ($this->isMethod('POST')) {
            $messages['imagen.required'] = 'La imagen del producto es obligatoria.';
        }
        
        $messages['imagen.image'] = 'El archivo debe ser una imagen válida (jpg, jpeg, png, webp).';
        $messages['imagen.mimes'] = 'La imagen debe ser de tipo: jpg, jpeg, png o webp.';
        $messages['imagen.max'] = 'La imagen no debe pesar más de 2MB.';
        
        return $messages;
    }

    public function attributes()
    {
        return [
            'nombre' => 'nombre del producto',
            'precio' => 'precio del producto',
            'descripcion' => 'descripción',
            'stock' => 'stock',
            'categoria_id' => 'categoría',
            'imagen' => 'imagen',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nombre' => trim($this->nombre),
            'descripcion' => $this->descripcion ? trim(strip_tags($this->descripcion)) : null,
        ]);
    }
}
