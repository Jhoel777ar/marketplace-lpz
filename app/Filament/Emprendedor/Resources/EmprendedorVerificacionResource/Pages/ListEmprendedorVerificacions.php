<?php

namespace App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource\Pages;

use App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmprendedorVerificacions extends ListRecords
{
    protected static string $resource = EmprendedorVerificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
