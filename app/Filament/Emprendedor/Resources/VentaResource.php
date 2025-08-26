<?php

namespace App\Filament\Emprendedor\Resources;

use App\Filament\Emprendedor\Resources\VentaResource\Pages;
use App\Filament\Emprendedor\Resources\VentaResource\RelationManagers;
use App\Models\Venta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VentaResource extends Resource
{
    protected static ?string $model = Venta::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pedidos';
    protected static ?string $pluralLabel = 'Pedidos';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('productos.producto', function ($query) {
                $query->where('emprendedor_id', auth()->id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('estado')
                ->label('Estado del Pedido')
                ->options([
                    'pendiente'   => 'Pendiente',
                    'preparando'  => 'Preparando',
                    'enviado'     => 'Enviado',
                    'entregado'   => 'Entregado',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')->searchable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Correo'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BOB'),

                Tables\Columns\BadgeColumn::make('estado')
                    ->colors([
                        'danger' => 'pendiente',
                        'warning' => 'preparando',
                        'info'    => 'enviado',
                        'success' => 'entregado',
                    ])
                    ->label('Estado'),

                Tables\Columns\TextColumn::make('envio.direccion')
                    ->label('Dirección de Envío'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Fecha'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente'   => 'Pendiente',
                        'preparando'  => 'Preparando',
                        'enviado'     => 'Enviado',
                        'entregado'   => 'Entregado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => true),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVentas::route('/'),
            'view'  => Pages\ViewVenta::route('/{record}'),
            'edit' => Pages\EditVenta::route('/{record}/edit'),
        ];
    }
}
