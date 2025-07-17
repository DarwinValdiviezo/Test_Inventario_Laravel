<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacturaEstadoController extends Controller
{
    /**
     * Firmar digitalmente una factura
     */
    public function firmar(Request $request, Factura $factura)
    {
        try {
            // Verificar que la factura esté pendiente de firma
            if (!$factura->isPendienteFirma()) {
                return redirect()->back()->with('error', 'La factura ya está firmada o no puede ser firmada.');
            }

            // Firmar la factura
            $factura->firmarDigitalmente();

            return redirect()->back()->with('success', 'Factura firmada digitalmente correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al firmar factura: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al firmar la factura: ' . $e->getMessage());
        }
    }

    /**
     * Emitir una factura (enviar por email)
     */
    public function emitir(Request $request, Factura $factura)
    {
        try {
            // Verificar que la factura esté firmada
            if (!$factura->isFirmada()) {
                return redirect()->back()->with('error', 'La factura debe estar firmada antes de emitirla.');
            }

            // Verificar que la factura no esté ya emitida
            if ($factura->isEmitida()) {
                return redirect()->back()->with('error', 'La factura ya ha sido emitida.');
            }

            // Emitir la factura
            $factura->emitir();

            // Aquí iría el envío por email
            // Por ahora solo actualizamos el estado

            return redirect()->back()->with('success', 'Factura emitida correctamente. Se enviará por email al cliente.');
        } catch (\Exception $e) {
            Log::error('Error al emitir factura: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al emitir la factura: ' . $e->getMessage());
        }
    }

    /**
     * Ver estado de una factura
     */
    public function estado(Factura $factura)
    {
        $estadoVisual = $factura->getEstadoVisual();
        
        return response()->json([
            'estado' => $estadoVisual,
            'isFirmada' => $factura->isFirmada(),
            'isEmitida' => $factura->isEmitida(),
            'isPendienteFirma' => $factura->isPendienteFirma(),
            'isPendienteEmision' => $factura->isPendienteEmision(),
            'fechaFirma' => $factura->fecha_firma,
            'fechaEmision' => $factura->fecha_emision_email
        ]);
    }
} 