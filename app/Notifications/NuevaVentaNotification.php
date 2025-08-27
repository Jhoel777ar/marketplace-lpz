<?php

namespace App\Notifications;

use App\Models\Venta;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class NuevaVentaNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public Venta $venta;

    public function __construct(Venta $venta)
    {
        $this->venta = $venta->load([
            'productos' => fn($query) => $query->select('id', 'venta_id', 'producto_id'),
            'productos.producto' => fn($query) => $query->select('id', 'nombre', 'emprendedor_id'),
        ]);

        if (!$this->venta->exists || !$this->venta->productos->first()?->producto?->emprendedor_id) {
            throw new \InvalidArgumentException('Venta o producto inválido');
        }
    }

    public function via($notifiable): array
    {
        if ($notifiable->id !== $this->venta->productos->first()->producto->emprendedor_id) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        $cacheKey = 'notificacion_venta_' . $this->venta->id;
        $message = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return FilamentNotification::make()
                ->title('📦 ¡Nuevo pedido recibido!')
                ->body(sprintf(
                    'Pedido #%d por Bs %.2f de %s. Revisa tus pedidos.',
                    $this->venta->id,
                    $this->venta->total,
                    $this->venta->productos->first()->producto->nombre
                ))
                ->success()
                ->icon('heroicon-o-bell')
                ->actions([
                    Action::make('view')
                        ->button()
                        ->label('Ver Pedido')
                        ->url(route('filament.emprendedor.resources.ventas.view', $this->venta->id), true),
                    Action::make('update')
                        ->color('secondary')
                        ->label('Actualizar Estado')
                        ->url(route('filament.emprendedor.resources.ventas.edit', $this->venta->id), true),
                ])
                ->getDatabaseMessage();
        });
        return $message;
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function viaQueues(): array
    {
        return [
            'database' => 'notifications',
            'broadcast' => 'notifications',
        ];
    }
}