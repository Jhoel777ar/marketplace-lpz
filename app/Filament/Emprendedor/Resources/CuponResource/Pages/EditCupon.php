<?php

namespace App\Filament\Emprendedor\Resources\CuponResource\Pages;

use App\Filament\Emprendedor\Resources\CuponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCupon extends EditRecord
{
    protected static string $resource = CuponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
