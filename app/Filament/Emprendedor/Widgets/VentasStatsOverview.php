<?php

namespace App\Filament\Emprendedor\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\VentaProducto;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VentasStatsOverview extends ChartWidget
{
    protected static ?string $heading = 'Ventas del Día';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $userId = Auth::id();
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $ventas = VentaProducto::query()
            ->whereHas('producto', fn($q) => $q->where('emprendedor_id', $userId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($v) => $v->created_at->format('Y-m-d'));

        $labels = [];
        $totalVentas = [];
        $cantidadProductos = [];

        for ($i = 0; $i <= 6; $i++) {
            $date = Carbon::now()->subDays(6 - $i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');

            if (isset($ventas[$date])) {
                $totalVentas[] = $ventas[$date]->sum('subtotal');
                $cantidadProductos[] = $ventas[$date]->sum('cantidad');
            } else {
                $totalVentas[] = 0;
                $cantidadProductos[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Monto total (BOB)',
                    'data' => $totalVentas,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Cantidad de productos vendidos',
                    'data' => $cantidadProductos,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                ],
            ],
        ];
    }
}
