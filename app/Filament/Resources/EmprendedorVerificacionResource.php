<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmprendedorVerificacionResource\Pages;
use App\Filament\Resources\EmprendedorVerificacionResource\RelationManagers;
use App\Models\EmprendedorVerificacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class EmprendedorVerificacionResource extends Resource
{
    protected static ?string $model = EmprendedorVerificacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Revisar cuentas';
    protected static ?string $navigationLabel = 'Validar cuentas';

    public static function getNavigationBadge(): ?string
    {
        return EmprendedorVerificacion::where('is_verified', false)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Revisión de cuenta')
                    ->schema([
                        Forms\Components\Placeholder::make('image_preview')
                            ->label('Documento enviado')
                            ->content(fn($record) => $record && $record->image_path
                                ? new HtmlString(
                                    '<a href="' . $record->image_path . '" target="_blank" rel="noopener noreferrer">
                <img src="' . $record->image_path . '" class="rounded-xl max-w-md shadow hover:scale-105 transition-transform"/>
            </a>'
                                )
                                : 'No se subió ninguna imagen')
                            ->extraAttributes(['class' => 'prose'])
                            ->hiddenLabel(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Cuenta aprobada')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $set('verified_at', now());
                                } else {
                                    $set('verified_at', null);
                                }
                            }),
                        Forms\Components\Hidden::make('verified_at')
                            ->default(now()),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Documento')
                    ->square(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Aprobado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Fecha verificación')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enviado el')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('is_verified', 'asc')
            ->filters([
                Tables\Filters\Filter::make('Fecha de envío')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($q) => $q->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn($q) => $q->whereDate('created_at', '<=', $data['created_until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make('ver')
                    ->label('Ver'),
                Tables\Actions\EditAction::make('revisar')
                    ->label('Revisar')
                    ->visible(fn($record) => !$record->is_verified),
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
            'index' => Pages\ListEmprendedorVerificacions::route('/'),
            'create' => Pages\CreateEmprendedorVerificacion::route('/create'),
            'edit' => Pages\EditEmprendedorVerificacion::route('/{record}/edit'),
        ];
    }
}
