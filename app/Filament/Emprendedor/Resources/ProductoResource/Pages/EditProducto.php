<?php

namespace App\Filament\Emprendedor\Resources\ProductoResource\Pages;

use App\Filament\Emprendedor\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected ?int $cuponSeleccionado = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->cuponSeleccionado = $data['cupon_id'] ?? null;
        unset($data['cupon_id']);
        return $data;
    }

    protected function afterSave(): void
    {
        if (!empty($this->cuponSeleccionado)) {
            $this->record->cupon()->detach(
                $this->record->cuponesActivos()->pluck('cupon_id')->toArray()
            );
            $this->record->cupon()->attach($this->cuponSeleccionado);

            Notification::make()
                ->title('Producto actualizado')
                ->success()
                ->body('Se aplicó el cupón seleccionado correctamente.')
                ->send();
        } else {
            Notification::make()
                ->title('Producto actualizado')
                ->success()
                ->body('El producto se actualizó sin aplicar cupón.')
                ->send();
        }
    }
}
