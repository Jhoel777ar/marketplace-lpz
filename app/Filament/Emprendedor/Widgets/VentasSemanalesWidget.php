<?php

namespace App\Filament\Emprendedor\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\VentaProducto;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VentasSemanalesWidget extends ChartWidget
{
    protected static ?string $heading = 'Ventas de la Semana';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $userId = Auth::id();
        $labels = [];
        $dataMonto = [];
        $dataCantidad = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            $ventas = VentaProducto::query()
                ->whereHas('producto', fn($q) => $q->where('emprendedor_id', $userId))
                ->whereDate('created_at', $date)
                ->get();
            $dataMonto[] = $ventas->sum('subtotal');
            $dataCantidad[] = $ventas->sum('cantidad');
        }
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Monto total (BOB)',
                    'data' => $dataMonto,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Cantidad de productos vendidos',
                    'data' => $dataCantidad,
                    'backgroundColor' => 'rgba(255, 206, 86, 0.5)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
