<?php

namespace App\Jobs;

use App\Mail\VentaExitosaMail;
use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoVenta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $venta;

    /**
     * Create a new job instance.
     */
    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->venta->user->email)->send(new VentaExitosaMail($this->venta));
    }
}
