<?php

namespace App\Notifications;

use App\Models\Reseña;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class NuevaReseñaNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public Reseña $reseña;

    public function __construct(Reseña $reseña)
    {
        $this->reseña = $reseña->load([
            'producto' => fn($query) => $query->select('id', 'nombre', 'emprendedor_id'),
            'usuario' => fn($query) => $query->select('id', 'name'),
        ]);

        if (!$this->reseña->exists || !$this->reseña->producto?->emprendedor_id) {
            throw new \InvalidArgumentException('Reseña o producto inválido');
        }
    }

    public function via($notifiable): array
    {
        if ($notifiable->id !== $this->reseña->producto->emprendedor_id) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        $cacheKey = 'notificacion_resena_' . $this->reseña->id;
        $message = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return FilamentNotification::make()
                ->title('🌟 ¡Nueva reseña recibida!')
                ->body(sprintf(
                    'El usuario %s dejó una reseña de %d estrella(s) para tu producto %s.',
                    $this->reseña->usuario->name,
                    $this->reseña->calificacion_producto,
                    $this->reseña->producto->nombre
                ))
                ->success()
                ->icon('heroicon-o-star')
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
