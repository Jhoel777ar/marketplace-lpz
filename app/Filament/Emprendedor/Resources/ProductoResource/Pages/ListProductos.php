<?php

namespace App\Filament\Emprendedor\Resources\ProductoResource\Pages;

use App\Filament\Emprendedor\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

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

    public function getTabs(): array
    {
        return [
            Tab::make('Todos')
                ->badge(ProductoResource::getModel()::ownedBy(auth()->id())->count())
                ->badgeColor('primary')
                ->modifyQueryUsing(fn(Builder $query) => $query),

            Tab::make('Destacados')
                ->badge(ProductoResource::getModel()::ownedBy(auth()->id())->where('destacado', true)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('destacado', true)),

            Tab::make('Públicos')
                ->badge(ProductoResource::getModel()::ownedBy(auth()->id())->where('publico', true)->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('publico', true)),

            Tab::make('Stock Bajo')
                ->badge(ProductoResource::getModel()::ownedBy(auth()->id())->where('stock', '<', 10)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('stock', '<', 10)),
        ];
    }
}
