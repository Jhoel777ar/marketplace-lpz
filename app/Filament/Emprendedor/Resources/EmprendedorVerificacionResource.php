<?php

namespace App\Filament\Emprendedor\Resources;

use App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource\Pages;
use App\Filament\Emprendedor\Resources\EmprendedorVerificacionResource\RelationManagers;
use App\Models\EmprendedorVerificacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class EmprendedorVerificacionResource extends Resource
{
    protected static ?string $model = EmprendedorVerificacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Verificaciones';
    protected static ?string $navigationLabel = 'Verificar cuenta';

    public static function getNavigationBadge(): ?string
    {
        if (!auth()->check()) return null;

        $pendientes = static::getModel()::ownedBy(auth()->id())
            ->where('is_verified', false)
            ->count();

        return $pendientes > 0 ? $pendientes : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de verificación')
                    ->description(
                        "Para acceder a los beneficios exclusivos de emprendedores estudiantiles, necesitamos verificar que eres un estudiante activo.\n\n" .
                            "Sube una foto clara de tu carnet universitario o documento oficial.\n\n" .
                            "✅ Tus datos están 100% protegidos y solo se usan para esta verificación. " .
                            "No se comparten con terceros ni se utilizan con otros fines."
                    )
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Carnet Universitario / Documento')
                            ->disk('s3')
                            ->directory('verificaciones')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->required()
                            ->columnSpan('full'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(EmprendedorVerificacion::ownedBy(auth()->id()))
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Documento')
                    ->circular()
                    ->height(60)
                    ->width(60)
                    ->openUrlInNewTab(),

                TextColumn::make('user.name')
                    ->label('Nombre del Emprendedor')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Correo')
                    ->searchable(),

                TextColumn::make('is_verified')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Verificado' : 'No verificado')
                    ->color(fn($state) => $state ? 'success' : 'danger'),

                TextColumn::make('verified_at')
                    ->label('Verificado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Solicitado el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->before(function ($record) {
                        try {
                            if ($record->image_path) {
                                $path = str_replace(
                                    'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/',
                                    '',
                                    $record->image_path
                                );
                                Storage::disk('s3')->delete($path);
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al borrar la imagen')
                                ->body('No se pudo eliminar la imagen de S3: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn($record) => !$record->is_verified)
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

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return !$user->verificacionEmprendedor()->where('is_verified', true)->exists();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmprendedorVerificacions::route('/'),
            'create' => Pages\CreateEmprendedorVerificacion::route('/create'),
        ];
    }
}
