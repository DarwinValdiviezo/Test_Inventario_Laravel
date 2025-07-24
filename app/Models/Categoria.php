<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int|null $productos_count
 */
class Categoria extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'descripcion', 'color', 'activo', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
