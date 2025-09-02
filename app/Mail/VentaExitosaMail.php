<?php

namespace App\Mail;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VentaExitosaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;

    /**
     * Create a new message instance.
     */
    public function __construct(Venta $venta)
    {
        // Cargar relaciones necesarias
        $this->venta = $venta->load(['user', 'productos.producto', 'envio']);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Factura de tu compra #' . $this->venta->id)
            ->view('emails.venta-exitosa')
            ->with([
                'venta' => $this->venta,
                'usuario' => $this->venta->user,
                'productos' => $this->venta->productos,
                'envio' => $this->venta->envio,
            ]);
    }
}
