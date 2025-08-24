<?php

namespace App\Filament\Resources\EmprendedorVerificacionResource\Pages;

use App\Filament\Resources\EmprendedorVerificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEmprendedorVerificacion extends EditRecord
{
    protected static string $resource = EmprendedorVerificacionResource::class;

    protected function beforeFill(): void
    {
        if ($this->record->is_verified) {
            Notification::make()
                ->title('Este registro ya fue verificado y no puede editarse.')
                ->warning()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        }
    }
}
