<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Categoria;
use App\Models\FacturaDetalle;
use App\Models\User;

/**
 * @property \App\Models\Categoria $categoria
 */
class Producto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'descripcion', 'imagen', 'categoria_id', 'stock', 'precio', 'created_by', 'updated_by'
    ];

    /**
     * Relación con la categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function facturaDetalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    /**
     * Relación con el usuario creador
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario modificador
     */
    public function modificador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
