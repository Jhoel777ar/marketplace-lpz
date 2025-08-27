<?php

namespace App\Filament\Emprendedor\Resources;

use App\Filament\Emprendedor\Resources\ProductoMasVendidosResource\Pages;
use App\Models\Producto;
use App\Models\VentaProducto;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\HtmlColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductoMasVendidosResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Productos Más Vendidos';
    protected static ?string $pluralLabel = 'Productos Más Vendidos';

    public static function getNavigationBadge(): ?string
    {
        if (!Auth::check()) {
            return null;
        }
        $ventasCount = VentaProducto::query()
            ->whereHas('producto', function ($query) {
                $query->where('emprendedor_id', Auth::id());
            })
            ->selectRaw('COUNT(DISTINCT venta_id) as total')
            ->value('total');
        return $ventasCount ? (string) $ventasCount : '0';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('emprendedor_id', auth()->id())
            ->withSum('ventas', 'cantidad')
            ->withCount([
                'ventas as ventas_unicas_count' => function ($query) {
                    $query->select(DB::raw('COUNT(DISTINCT venta_id)'));
                }
            ])
            ->withCount('resenas')
            ->with(['resenas.usuario'])
            ->orderByDesc('ventas_sum_cantidad');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('imagen_principal')
                    ->label('Imagen')
                    ->getStateUsing(function ($record) {
                        return $record->imagenes->first()?->ruta;
                    })
                    ->circular()
                    ->square()
                    ->size(50),
                TextColumn::make('nombre')->label('Producto')->sortable(),
                TextColumn::make('precio')->money('BOB')->sortable(),
                TextColumn::make('stock')->sortable(),
                TextColumn::make('ventas_sum_cantidad')->label('Cantidad Vendida')->sortable(),
                TextColumn::make('ventas_ids')
                    ->label('Ventas ID')
                    ->getStateUsing(function ($record) {
                        $ids = $record->ventas()->pluck('venta_id')->unique()->toArray();
                        return $ids ? implode(', ', $ids) : 'Sin ventas';
                    }),
                TextColumn::make('resenas')
                    ->label('Promedio Estrellas')
                    ->formatStateUsing(
                        fn($record) => $record->resenas_count
                            ? str_repeat('⭐', round($record->resenas->avg('calificacion_producto')))
                            . " (" . round($record->resenas->avg('calificacion_producto'), 1) . ")"
                            : 'Sin reseñas'
                    ),
                TextColumn::make('resenas_detalle')
                    ->label('Últimas Reseñas')
                    ->formatStateUsing(function ($record) {
                        $html = '';
                        foreach ($record->resenas->take(3) as $resena) {
                            $html .= "<div class='border-b border-gray-200 py-1'>";
                            $html .= "<strong>{$resena->usuario->name}:</strong> ";
                            $html .= str_repeat('⭐', $resena->calificacion_producto);
                            $html .= "<p>{$resena->reseña}</p></div>";
                        }
                        return $html ?: '<span>Sin reseñas</span>';
                    })
                    ->html(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductoMasVendidos::route('/'),
        ];
    }
}
