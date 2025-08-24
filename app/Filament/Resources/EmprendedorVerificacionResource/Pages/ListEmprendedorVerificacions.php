<?php

namespace App\Filament\Resources\EmprendedorVerificacionResource\Pages;

use App\Filament\Resources\EmprendedorVerificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\EmprendedorVerificacion;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\StatsOverviewWidget\StatsOverviewWidget;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmprendedorVerificacions extends ListRecords
{
    protected static string $resource = EmprendedorVerificacionResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(EmprendedorVerificacion::count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn(Builder $query) => $query),

            'verified' => Tab::make('Verificados')
                ->badge(EmprendedorVerificacion::where('is_verified', true)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_verified', true)),

            'pending' => Tab::make('Pendientes')
                ->badge(EmprendedorVerificacion::where('is_verified', false)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_verified', false)),
        ];
    }
}
