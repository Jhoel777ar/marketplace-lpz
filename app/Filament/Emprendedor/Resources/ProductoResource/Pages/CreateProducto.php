<?php

namespace App\Filament\Emprendedor\Resources\ProductoResource\Pages;

use App\Filament\Emprendedor\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

    protected ?int $cuponSeleccionado = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->cuponSeleccionado = $data['cupon_id'] ?? null;
        unset($data['cupon_id']);
        return $data;
    }

    protected function afterCreate(): void
    {
        if (isset($this->data['imagenes'])) {
            foreach ($this->data['imagenes'] as $ruta) {
                $this->record->imagenes()->create(['ruta' => $ruta]);
            }
        }
        if (!empty($this->cuponSeleccionado)) {
            $this->record->cupon()->attach($this->cuponSeleccionado);
            Notification::make()
                ->title('Producto creado')
                ->success()
                ->body('El producto ha sido creado y se aplicó el cupón seleccionado.')
                ->send();
        } else {
            Notification::make()
                ->title('Producto creado')
                ->success()
                ->body('El producto ha sido creado sin cupón.')
                ->send();
        }
    }
}
