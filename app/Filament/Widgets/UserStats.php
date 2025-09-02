<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Carrito;
use Carbon\Carbon;

class UserStats extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        $now = Carbon::now();
        $startWeek = $now->copy()->startOfWeek();
        $startMonth = $now->copy()->startOfMonth();
        $startPrevMonth = $now->copy()->subMonth()->startOfMonth();
        $endPrevMonth = $now->copy()->subMonth()->endOfMonth();
        $daysInWeek = $now->diffInDays($startWeek) + 1;

        $totalUsers = User::count();
        $usersWeek = User::where('created_at', '>=', $startWeek)->count();
        $usersMonth = User::where('created_at', '>=', $startMonth)->count();
        $usersPrevMonth = User::whereBetween('created_at', [$startPrevMonth, $endPrevMonth])->count();
        $avgDailyUsers = $daysInWeek > 0 ? $usersWeek / $daysInWeek : 0;
        $predNextWeekUsers = round($avgDailyUsers * 7);

        $totalProducts = Producto::count();
        $productsWeek = Producto::where('created_at', '>=', $startWeek)->count();
        $productsMonth = Producto::where('created_at', '>=', $startMonth)->count();

        $totalSales = Venta::count();
        $salesWeek = Venta::where('created_at', '>=', $startWeek)->count();
        $salesMonth = Venta::where('created_at', '>=', $startMonth)->count();
        $totalSalesAmount = Venta::sum('total');
        $salesAmountWeek = Venta::where('created_at', '>=', $startWeek)->sum('total');
        $salesAmountMonth = Venta::where('created_at', '>=', $startMonth)->sum('total');

        $totalCarts = Carrito::count();

        return [
            Stat::make('Total Usuarios', $totalUsers)
                ->description('Registrados en total'),
            Stat::make('Usuarios Semana', $usersWeek)
                ->description('Registrados esta semana'),
            Stat::make('Usuarios Mes', $usersMonth)
                ->description('Registrados este mes'),
            Stat::make('Usuarios Mes Anterior', $usersPrevMonth)
                ->description('Registrados mes pasado'),
            Stat::make('Predicción Usuarios Próxima Semana', $predNextWeekUsers)
                ->description('Usuarios estimados'),
            Stat::make('Total Productos', $totalProducts)
                ->description('Registrados en total'),
            Stat::make('Productos Semana', $productsWeek)
                ->description('Registrados esta semana'),
            Stat::make('Productos Mes', $productsMonth)
                ->description('Registrados este mes'),
            Stat::make('Total Ventas', $totalSales)
                ->description('Realizadas en total'),
            Stat::make('Ventas Semana', $salesWeek)
                ->description('Realizadas esta semana'),
            Stat::make('Ventas Mes', $salesMonth)
                ->description('Realizadas este mes'),
            Stat::make('Monto Total Ventas', 'BOB ' . number_format($totalSalesAmount, 2))
                ->description('Generado por emprendedores'),
            Stat::make('Monto Ventas Semana', 'BOB ' . number_format($salesAmountWeek, 2))
                ->description('Generado esta semana'),
            Stat::make('Monto Ventas Mes', 'BOB ' . number_format($salesAmountMonth, 2))
                ->description('Generado este mes'),
            Stat::make('Total Carritos', $totalCarts)
                ->description('Carritos activos'),
        ];
    }
}
