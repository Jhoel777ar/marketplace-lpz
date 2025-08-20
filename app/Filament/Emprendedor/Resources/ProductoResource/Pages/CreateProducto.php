<?php

namespace App\Filament\Emprendedor\Resources\ProductoResource\Pages;

use App\Filament\Emprendedor\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

    protected function afterCreate(): void
    {
        $bucketUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
        foreach ($this->record->imagenes as $imagen) {
            $imagen->ruta = $bucketUrl . $imagen->ruta;
            $imagen->save();
        }
    }
}
