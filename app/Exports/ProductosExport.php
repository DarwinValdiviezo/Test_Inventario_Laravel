<?php
namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * @property \App\Models\Categoria|null $categoria
 */
class ProductosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Producto::with('categoria')->get()->map(function($p) {
            return [
                'ID' => $p->id,
                'Nombre' => $p->nombre,
                'Categoría' => $p->categoria->nombre ?? 'Sin categoría',
                'Stock' => $p->stock,
                'Precio' => $p->precio,
                'Estado' => $p->deleted_at ? 'Eliminado' : 'Activo',
            ];
        });
    }
    public function headings(): array
    {
        return ['ID', 'Nombre', 'Categoría', 'Stock', 'Precio', 'Estado'];
    }
} 