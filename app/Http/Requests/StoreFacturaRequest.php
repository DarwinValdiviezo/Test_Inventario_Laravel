<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacturaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => [
                'required',
                'integer',
                'exists:clientes,id',
                Rule::exists('clientes', 'id')->where(function ($query) {
                    $query->where('estado', 'activo');
                }),
            ],
            'productos' => [
                'required',
                'array',
                'min:1',
            ],
            'productos.*.producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
                Rule::exists('productos', 'id')->where(function ($query) {
                    $query->where('stock', '>', 0);
                }),
            ],
            'productos.*.cantidad' => [
                'required',
                'integer',
                'min:1',
                'max:9999',
            ],
            'forma_pago' => [
                'nullable',
                'string',
                'in:EFECTIVO,TARJETA,TRANSFERENCIA,CHEQUE,OTROS',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe o no está activo.',
            'productos.required' => 'Debe agregar al menos un producto a la factura.',
            'productos.min' => 'Debe agregar al menos un producto a la factura.',
            'productos.*.producto_id.required' => 'Debe seleccionar un producto.',
            'productos.*.producto_id.exists' => 'El producto seleccionado no existe o no tiene stock disponible.',
            'productos.*.cantidad.required' => 'Debe especificar la cantidad del producto.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad mínima es 1.',
            'productos.*.cantidad.max' => 'La cantidad máxima es 9999.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar stock disponible para cada producto
            if ($this->has('productos')) {
                foreach ($this->input('productos', []) as $index => $producto) {
                    if (isset($producto['producto_id']) && isset($producto['cantidad'])) {
                        $productoModel = \App\Models\Producto::find($producto['producto_id']);
                        if ($productoModel && $productoModel->stock < $producto['cantidad']) {
                            $validator->errors()->add(
                                "productos.{$index}.cantidad",
                                "Stock insuficiente para {$productoModel->nombre}. Disponible: {$productoModel->stock}"
                            );
                        }
                    }
                }
            }

            // Validar que no haya productos duplicados
            if ($this->has('productos')) {
                $productoIds = collect($this->input('productos', []))->pluck('producto_id');
                $duplicates = $productoIds->duplicates();
                if ($duplicates->count() > 0) {
                    $validator->errors()->add(
                        'productos',
                        'No puede agregar el mismo producto más de una vez.'
                    );
                }
            }
        });
    }
}
