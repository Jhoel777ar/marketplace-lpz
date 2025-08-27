<?php

namespace App\Filament\Emprendedor\Resources\VentaResource\Pages;

use App\Filament\Emprendedor\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;

class ListVentas extends ListRecords
{
    protected static string $resource = VentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportar')
                ->label('Exportar Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->action(function () {
                    return Excel::download(new VentasExport, 'ventas.xlsx');
                }),
        ];
    }
}
