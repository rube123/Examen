<?php

namespace App\Http\Controllers;

use App\Models\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryHistoryController extends Controller
{
    // Ver historial por copia
    public function show($inventory_id)
    {
        $historial = InventoryHistory::where('inventory_id', $inventory_id)
            ->orderByDesc('fecha_evento')
            ->get();

        return view('empleado.historial', compact('historial'));
    }

    // Registrar devolución o daño
    public function store(Request $request)
{
    $request->validate([
        'inventory_id' => 'required|exists:inventory,inventory_id',
        'estado' => 'required|string',
        'observaciones' => 'nullable|string',
    ]);

    // 🔹 Guardar en el historial
    DB::table('inventory_history')->insert([
        'inventory_id' => $request->inventory_id,
        'estado' => $request->estado,
        'observaciones' => $request->observaciones,
        'fecha_evento' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 🔹 Buscar la renta pendiente (sin return_date)
    $renta = DB::table('rental')
        ->where('inventory_id', $request->inventory_id)
        ->whereNull('return_date')
        ->orderByDesc('rental_date')
        ->first();

    if ($renta) {
        // Actualizar la devolución en rental
        DB::table('rental')
            ->where('rental_id', $renta->rental_id)
            ->update(['return_date' => now(), 'last_update' => now()]);

        // Si la película está dañada o perdida, actualizar inventario
        if (in_array($request->estado, ['Dañado', 'Perdido'])) {
            DB::table('inventory')
                ->where('inventory_id', $request->inventory_id)
                ->update(['last_update' => now()]);
        }
    }

    return redirect()->route('empleado.rentas')
        ->with('status', 'Devolución registrada correctamente y actualizada en historial.');
}


}
