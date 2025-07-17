<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'descripcion', 'imagen', 'categoria_id', 'stock', 'precio', 'created_by', 'updated_by'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function facturaDetalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modificador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
