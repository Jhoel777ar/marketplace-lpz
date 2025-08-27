<?php

namespace App\Filament\Emprendedor\Resources\VentaResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;

class ProductosRelationManager extends RelationManager
{
    protected static string $relationship = 'productos';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('producto.nombre')->label('Producto')->searchable(),
                Tables\Columns\ImageColumn::make('producto.imagenes')
                    ->label('Imágenes')
                    ->getStateUsing(function ($record) {
                        return $record->producto->imagenes->first()?->ruta;
                    })
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('cantidad'),
                Tables\Columns\TextColumn::make('subtotal')->money('BOB'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
