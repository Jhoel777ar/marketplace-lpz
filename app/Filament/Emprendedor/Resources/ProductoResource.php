<?php

namespace App\Filament\Emprendedor\Resources;

use App\Filament\Emprendedor\Resources\ProductoResource\Pages;
use App\Filament\Emprendedor\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('precio')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('categoria')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('destacado')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_publicacion')
                    ->required(),
                Forms\Components\Hidden::make('emprendedor_id')
                    ->default(fn() => Auth::id()),
                Forms\Components\Repeater::make('imagenes')
                    ->label('Imágenes del Producto')
                    ->relationship()
                    ->maxItems(5)
                    ->schema([
                        Forms\Components\FileUpload::make('ruta')
                            ->disk('s3')
                            ->directory('productos')
                            ->image()
                            ->visibility('public')
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->required(),
                    ])
                    ->columnSpan('full'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('precio')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('imagenes.ruta')
                    ->label('Imágenes')
                    ->formatStateUsing(function ($state, $record) {
                        $html = '';
                        foreach ($record->imagenes as $imagen) {
                            $html .= '<img src="' . $imagen->ruta . '" style="width:50px; height:50px; margin-right:5px; border-radius:5px;">';
                        }
                        return $html;
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('categoria')
                    ->searchable(),
                Tables\Columns\IconColumn::make('destacado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('fecha_publicacion')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('emprendedor_id')
                    ->numeric()
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
