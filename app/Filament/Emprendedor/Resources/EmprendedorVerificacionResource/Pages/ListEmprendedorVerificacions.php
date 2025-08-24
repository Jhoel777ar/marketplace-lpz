<?php

namespace App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource\Pages;

use App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListEmprendedorVerificacions extends ListRecords
{
    protected static string $resource = EmprendedorVerificacionResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $verificacion = $user->verificacionEmprendedor()->latest()->first();

        if (!$verificacion) {
            Notification::make()
                ->title('Cuenta pendiente de verificación')
                ->body('Debes enviar tu documento.')
                ->warning()
                ->send();
        } elseif ($verificacion->is_verified == 0) {
            Notification::make()
                ->title('Esperando aprobación')
                ->body('Tu documento ya fue enviado, espera a que sea aprobado.')
                ->warning()
                ->send();
        }

        return [
            Actions\CreateAction::make(),
        ];
    }
}
