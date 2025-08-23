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
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Count;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Inventario';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return Auth::check() ? static::getModel()::where('emprendedor_id', Auth::id())->count() : null;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->ownedBy(auth()->id());
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del Producto')
                    ->required()
                    ->maxLength(255)
                    ->reactive()
                    ->columnSpan('full'),
                Forms\Components\RichEditor::make('descripcion')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('stock')
                    ->label('Stock del Producto')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Cantidad disponible del producto en inventario'),
                Forms\Components\TextInput::make('precio')
                    ->label('Precio (Bs.)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->step(0.01)
                    ->prefix('Bs. '),
                Forms\Components\Toggle::make('destacado')
                    ->label('Producto Destacado')
                    ->helperText('Activa esto si quieres que este producto aparezca en la sección de destacados.')
                    ->default(false)
                    ->required(),
                Forms\Components\Toggle::make('publico')
                    ->label('¿Producto Público?')
                    ->helperText('Si está apagado, el producto será privado por defecto.')
                    ->default(false)
                    ->required(),
                Forms\Components\Select::make('categorias')
                    ->relationship('categorias', 'nombre')
                    ->multiple()
                    ->searchable()
                    ->required()
                    ->columnSpan('full')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->nombre),
                Forms\Components\Hidden::make('fecha_publicacion')
                    ->default(now()),
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
                    ->columnSpan('full')
                    ->visible(fn($record) => $record === null),
                //solo para entonos prueba
                /*
                Forms\Components\Repeater::make('imagenes')
                    ->label('Imágenes del Producto')
                    ->relationship()
                    ->maxItems(5)
                    ->schema([
                        Forms\Components\TextInput::make('ruta')
                            ->label('URL de la Imagen')
                            ->placeholder('https://example.com/imagen.jpg')
                            ->url()
                            ->required(),
                    ])
                    ->columnSpan('full'),
                */
                Forms\Components\Section::make('Fechas del Producto')
                    ->description('Información de creación y última actualización')
                    ->columns(2)
                    ->visible(fn($record) => $record !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Fecha de Creación')
                            ->content(fn($get) => $get('created_at') ? Carbon::parse($get('created_at'))->format('d/m/Y H:i') : null),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Última Actualización')
                            ->content(fn($get) => $get('updated_at') ? Carbon::parse($get('updated_at'))->format('d/m/Y H:i') : null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Producto::ownedBy(auth()->id()))
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->summarize([
                        Count::make()->label('Total Productos'),
                    ]),
                Tables\Columns\TextColumn::make('precio')
                    ->numeric()
                    ->sortable()
                    ->summarize([
                        Sum::make()->money('Bs. '),
                        Count::make()->label('Total Productos'),
                    ]),
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
                Tables\Columns\TextColumn::make('categorias')
                    ->label('Categorías')
                    ->formatStateUsing(function ($record) {
                        return $record->categorias
                            ->unique('id')
                            ->pluck('nombre')
                            ->map(fn($nombre) => "<span class='inline-block bg-blue-500 text-white text-xs px-2 py-0.5 rounded mr-1'>$nombre</span>")
                            ->join('|');
                    })
                    ->html()
                    ->badge(),
                Tables\Columns\IconColumn::make('destacado')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('publico')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->icon(fn($state) => $state <= 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->formatStateUsing(function ($state) {
                        if ($state <= 0) {
                            return 'Stock Agotado';
                        } elseif ($state < 10) {
                            return 'Stock Bajo';
                        } else {
                            return 'En Stock';
                        }
                    })
                    ->colors([
                        'danger' => fn($state) => $state <= 0,
                        'warning' => fn($state) => $state > 0 && $state < 10,
                        'success' => fn($state) => $state >= 10,
                    ])
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('fecha_publicacion')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('emprendedor.name')
                    ->label('Usuario')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
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
                Tables\Filters\Filter::make('created_at')
                    ->label('Fecha de Creación')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn($state) => 'Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn($state) => 'Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
                            ->orderBy('created_at', 'desc');
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Desde ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Hasta ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label('Fecha de Creación')
                    ->date()
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagenesRelationManager::class,
        ];
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        $verificacion = $user->verificacionEmprendedor;
        return $verificacion && $verificacion->is_verified;
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
