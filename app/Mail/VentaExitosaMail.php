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
        $this->venta = $venta;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Compra realizada con éxito')
                    ->view('emails.venta-exitosa')
                    ->with([
                        'ventaId' => $this->venta->id,
                        'usuarioNombre' => $this->venta->user->name,
                        'total' => $this->venta->total,
                    ]);
    }
}
