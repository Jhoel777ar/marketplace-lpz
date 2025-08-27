<?php

namespace App\Exports;

use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VentasExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $rowCount = 0;
    protected $totalGeneral = 0;
    protected $cantidadTotal = 0;

    public function collection()
    {
        $data = collect();
        $ventas = Venta::whereHas('productos.producto', function ($query) {
            $query->where('emprendedor_id', Auth::id());
        })
            ->with(['user', 'productos.producto', 'envio'])
            ->get();
        foreach ($ventas as $venta) {
            foreach ($venta->productos as $productoVenta) {
                if ($productoVenta->producto->emprendedor_id !== Auth::id()) {
                    continue;
                }
                $data->push([
                    'venta_id'      => $venta->id,
                    'cliente'       => $venta->user->name,
                    'correo'        => $venta->user->email,
                    'producto'      => $productoVenta->producto->nombre,
                    'precio'        => number_format($productoVenta->producto->precio, 2),
                    'cantidad'      => $productoVenta->cantidad,
                    'subtotal'      => number_format($productoVenta->subtotal, 2),
                    'estado'        => $venta->estado,
                    'direccion'     => $venta->envio?->direccion ?? '',
                    'fecha'         => $venta->created_at->format('d/m/Y H:i'),
                    'total_venta'   => number_format($venta->total, 2),
                ]);
                $this->cantidadTotal += $productoVenta->cantidad;
            }
            $this->totalGeneral += $venta->total;
        }
        $this->rowCount = $data->count();
        return $data;
    }

    public function headings(): array
    {
        return [
            'Venta ID',
            'Cliente',
            'Correo',
            'Producto',
            'Precio Unitario',
            'Cantidad',
            'Subtotal',
            'Estado',
            'Dirección',
            'Fecha',
            'Total Venta',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rowCount + 1;
                $sheet->setCellValue('I' . ($lastRow + 2), 'TOTAL GENERAL:');
                $sheet->setCellValue('J' . ($lastRow + 2), number_format($this->totalGeneral, 2));
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'wrapText'   => true,
                    ],
                ];
                $sheet->getStyle('A1:K' . ($lastRow + 2))->applyFromArray($styleArray);
                $sheet->getStyle('A1:K1')->getFont()->setBold(true);
                $sheet->getStyle('I' . ($lastRow + 2) . ':J' . ($lastRow + 2))
                    ->getFont()->setBold(true);
                $sheet->getStyle('I' . ($lastRow + 2) . ':J' . ($lastRow + 2))
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFE699');
            },
        ];
    }
}
