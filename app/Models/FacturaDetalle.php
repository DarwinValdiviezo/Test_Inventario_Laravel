<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaDetalle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'factura_id', 'producto_id', 'cantidad', 'precio_unitario', 'subtotal', 'created_by', 'updated_by'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
