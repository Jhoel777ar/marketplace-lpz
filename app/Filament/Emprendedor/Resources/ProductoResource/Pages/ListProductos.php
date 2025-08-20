<?php

namespace App\Filament\Emprendedor\Resources\ProductoResource\Pages;

use App\Filament\Emprendedor\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
