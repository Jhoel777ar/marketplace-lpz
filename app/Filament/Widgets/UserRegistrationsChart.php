<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Carbon\Carbon;

class UserRegistrationsChart extends ChartWidget
{
     protected static ?int $sort = 3;
    protected static ?string $heading = 'Registros de Usuarios';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $now = Carbon::now();
        $startPast = $now->copy()->subDays(7)->startOfDay();
        $endFuture = $now->copy()->addDays(7)->endOfDay();

        $users = User::whereBetween('created_at', [$startPast, $now])
            ->get()
            ->groupBy(fn($u) => $u->created_at->format('Y-m-d'));

        $pastData = [];
        $n = 0;
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = -7; $i < 0; $i++) {
            $date = $now->copy()->addDays($i)->format('Y-m-d');
            $y = isset($users[$date]) ? count($users[$date]) : 0;
            $x = $i;
            $pastData[$date] = $y;

            $n++;
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $labels = [];
        $pastCounts = [];
        $predictedCounts = [];

        for ($i = -7; $i <= 7; $i++) {
            $date = $now->copy()->addDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');

            if ($i <= 0) {
                $pastCounts[] = isset($pastData[$date]) ? $pastData[$date] : 0;
                $predictedCounts[] = null;
            } else {
                $pastCounts[] = null;
                $x = $i;
                $predicted = round($slope * $x + $intercept);
                $predictedCounts[] = max(0, $predicted);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Registros Reales',
                    'data' => $pastCounts,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                [
                    'label' => 'Predicción',
                    'data' => $predictedCounts,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderDash' => [5, 5],
                ],
            ],
        ];
    }
}