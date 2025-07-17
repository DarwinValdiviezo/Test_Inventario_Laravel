<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'email', 'password', 'telefono', 'direccion', 'estado', 'created_by', 'updated_by', 'user_id'
    ];

    protected $hidden = [
        'password',
    ];

    protected $dates = ['deleted_at'];

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método para verificar si el cliente está eliminado
    public function isDeleted()
    {
        return $this->trashed();
    }
}
