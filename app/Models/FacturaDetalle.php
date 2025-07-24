<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Factura;
use App\Models\Producto;

/**
 * @property \App\Models\Factura|null $factura
 * @property \App\Models\Producto|null $producto
 */
class FacturaDetalle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'factura_id', 'producto_id', 'cantidad', 'precio_unitario', 'subtotal', 'created_by', 'updated_by'
    ];

    /**
     * Relación con la factura
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Relación con el producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
