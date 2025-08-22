<?php

namespace App\Filament\Emprendedor\Resources\ProductoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class ImagenesRelationManager extends RelationManager
{
    protected static string $relationship = 'imagenes';

    //protected static ?string $recordTitleAttribute = 'ruta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('ruta')
                    ->label('Imagen del Producto')
                    ->disk('s3')
                    ->directory('productos')
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->visibility('public')
                    ->maxSize(4096)
                    ->imagePreviewHeight('250')
                    ->panelLayout('centered')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        $extension = $file->getClientOriginalExtension();
                        return Str::uuid() . '.' . $extension;
                    })
                    ->required()
                    ->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('ruta')
                    ->label('Vista previa')
                    ->disk('s3')
                    ->size(200)
                    ->url(fn($record) => $record->ruta, true)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('id')
                    ->label('Imagen #')
                    ->formatStateUsing(fn($state) => "Imagen {$state}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Subida el')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Subir Imagen')
                    ->visible(fn($livewire) => $livewire->ownerRecord->imagenes()->count() < 5),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Cambiar Imagen'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->before(function ($record) {
                        try {
                            if ($record->ruta) {
                                $path = str_replace(
                                    'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/',
                                    '',
                                    $record->ruta
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
                    }),
            ]);
    }
}
