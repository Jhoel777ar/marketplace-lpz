<?php

namespace App\Filament\Emprendedor\Resources\ProductoResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReseñasRelationManager extends RelationManager
{
    protected static string $relationship = 'resenas';

    protected static ?string $recordTitleAttribute = 'reseña';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('calificacion_producto')
                    ->label('Calificación Producto')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state)),
                Tables\Columns\TextColumn::make('calificacion_servicio')
                    ->label('Calificación Servicio')
                    ->formatStateUsing(fn($state) => $state ? str_repeat('⭐', $state) : 'N/A'),
                Tables\Columns\TextColumn::make('reseña')
                    ->label('Reseña')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\IconColumn::make('aprobada')
                    ->label('Aprobada')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->colors([
                        'success' => fn($state) => $state,
                        'danger' => fn($state) => !$state,
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('aprobada')
                    ->label('Solo aprobadas')
                    ->query(fn($query) => $query->where('aprobada', true)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar Reseña'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(function ($record, $form) {
                        return $form->schema([
                            Forms\Components\Textarea::make('reseña')
                                ->label('Reseña')
                                ->maxLength(200)
                                ->nullable()
                                ->disabled(fn() => $record->user_id !== Auth::id()),

                            Forms\Components\TextInput::make('calificacion_producto')
                                ->label('Calificación Producto')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(5)
                                ->required()
                                ->disabled(fn() => $record->user_id !== Auth::id()),

                            Forms\Components\TextInput::make('calificacion_servicio')
                                ->label('Calificación Servicio')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(5)
                                ->nullable()
                                ->disabled(fn() => $record->user_id !== Auth::id()),

                            Forms\Components\Toggle::make('aprobada')
                                ->label('Aprobada')
                                ->default(false)
                                ->disabled(fn() => $record->user_id === Auth::id()),
                        ]);
                    })
                    ->visible(fn($record) => $record->user_id === Auth::id() || $record->aprobada === 0),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->user_id === Auth::id())
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => Auth::id()),
                Forms\Components\Hidden::make('emprendedor_id')
                    ->default(fn() => Auth::id()),
                Forms\Components\TextInput::make('calificacion_producto')
                    ->label('Calificación Producto')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Forms\Components\TextInput::make('calificacion_servicio')
                    ->label('Calificación Servicio')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->nullable(),
                Forms\Components\Textarea::make('reseña')
                    ->label('Reseña')
                    ->maxLength(200)
                    ->nullable(),
                Forms\Components\Toggle::make('aprobada')
                    ->label('Aprobada')
                    ->default(false),
            ]);
    }
}
