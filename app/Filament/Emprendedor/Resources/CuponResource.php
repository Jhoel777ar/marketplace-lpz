<?php

namespace App\Filament\Emprendedor\Resources;

use App\Filament\Emprendedor\Resources\CuponResource\Pages;
use App\Filament\Emprendedor\Resources\CuponResource\RelationManagers;
use App\Models\Cupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CuponResource extends Resource
{
    protected static ?string $model = Cupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';
    protected static ?string $navigationGroup = 'Descuentos Aplicables';
    protected static ?string $navigationLabel = 'Cupones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => Auth::id()),
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(255)
                    ->suffix('cupón'),
                Forms\Components\TextInput::make('descuento')
                    ->required()
                    ->numeric()
                    ->suffix('%'),
                Forms\Components\TextInput::make('limite_usos')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->suffix('usos'),
                Forms\Components\DateTimePicker::make('fecha_vencimiento'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('¡Código copiado!')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('descuento')
                    ->numeric()
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('limite_usos')
                    ->numeric()
                    ->color(fn($record) => $record->usos_realizados >= $record->limite_usos ? 'danger' : 'success')
                    ->icon(fn($record) => $record->usos_realizados >= $record->limite_usos ? 'heroicon-s-exclamation-circle' : 'heroicon-s-check-circle')
                    ->sortable(),
                Tables\Columns\TextColumn::make('usos_realizados')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCupons::route('/'),
            'create' => Pages\CreateCupon::route('/create'),
            'edit' => Pages\EditCupon::route('/{record}/edit'),
        ];
    }
}
