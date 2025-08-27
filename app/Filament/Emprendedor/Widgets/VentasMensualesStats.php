<?php

namespace App\Filament\Emprendedor\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\VentaProducto;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VentasMensualesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $ahora = Carbon::now();

        $inicioHoy = $ahora->copy()->startOfDay();
        $finHoy = $ahora->copy()->endOfDay();

        $inicioSemana = $ahora->copy()->subDays(6)->startOfDay(); 
        $inicioMes = $ahora->copy()->startOfMonth()->startOfDay();

        $ventasSemana = VentaProducto::query()
            ->whereHas('producto', fn($q) => $q->where('emprendedor_id', $userId))
            ->whereBetween('created_at', [$inicioSemana, $finHoy])
            ->get();

        $ventasMes = VentaProducto::query()
            ->whereHas('producto', fn($q) => $q->where('emprendedor_id', $userId))
            ->whereBetween('created_at', [$inicioMes, $finHoy])
            ->get();

        return [

            Stat::make('Ventas Semana', $ventasSemana->count())
                ->description('Cantidad de productos vendidos esta semana'),

            Stat::make('Monto Semana', 'BOB ' . number_format($ventasSemana->sum('subtotal'), 2))
                ->description('Monto total de la semana'),

            Stat::make('Ventas Mes', $ventasMes->count())
                ->description('Cantidad de productos vendidos este mes'),

            Stat::make('Monto Mes', 'BOB ' . number_format($ventasMes->sum('subtotal'), 2))
                ->description('Monto total del mes'),
        ];
    }
}
