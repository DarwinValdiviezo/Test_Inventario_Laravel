<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Auditoria extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 'old_values', 'new_values', 'description', 'observacion', 'ip_address', 'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getClienteNombre()
    {
        if ($this->model_type === Cliente::class) {
            $cliente = Cliente::withTrashed()->find($this->model_id);
            return $cliente ? $cliente->nombre : 'Cliente no encontrado';
        }
        return 'N/A';
    }

    public function getAfectado()
    {
        if ($this->model_type === \App\Models\Cliente::class) {
            $cliente = \App\Models\Cliente::withTrashed()->find($this->model_id);
            return $cliente ? $cliente->nombre : 'Cliente no encontrado';
        }
        if ($this->model_type === \App\Models\Producto::class) {
            $producto = \App\Models\Producto::withTrashed()->find($this->model_id);
            return $producto ? $producto->nombre : 'Producto no encontrado';
        }
        if ($this->model_type === \App\Models\User::class) {
            $user = \App\Models\User::withTrashed()->find($this->model_id);
            return $user ? $user->name : 'Usuario no encontrado';
        }
        if ($this->model_type === \App\Models\Factura::class) {
            $factura = \App\Models\Factura::withTrashed()->find($this->model_id);
            return $factura ? 'Factura #' . $factura->id : 'Factura no encontrada';
        }
        return 'ID: ' . $this->model_id;
    }
}
