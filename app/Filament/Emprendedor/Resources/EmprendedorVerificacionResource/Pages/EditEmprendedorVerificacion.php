<?php

namespace App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource\Pages;

use App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmprendedorVerificacion extends EditRecord
{
    protected static string $resource = EmprendedorVerificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
