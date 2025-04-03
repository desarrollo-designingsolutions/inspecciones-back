<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Carbon\Carbon;

class VehicleDesignExport implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;

    public $data;
    public $inspections;

    public function __construct($data, $inspections)
    {
        $this->data = $data;
        $this->inspections = $inspections;
    }

    public function view(): View
    {
        // Mapeo de nombres de meses en español a sus números correspondientes
        $monthNames = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        // Calcular los días del mes seleccionado
        $daysInMonth = Carbon::create($this->data['year'], $this->data['month'], 1)->daysInMonth;

        return view(
            'Exports.Vehicle.VehicleDesignExportExcel',
            [
                'data' => [
                    'license_plate' => $this->data['license_plate'],
                    'month' => $monthNames[$this->data['month']],
                    'year' => $this->data['year'],
                    'days' => $daysInMonth,
                ],
                'inspections' => $this->inspections,
            ]
        );
    }

    public function registerEvents(): array
    {
        return [
            // AfterSheet::class => function (AfterSheet $event) {
            //     // Obtener el objeto hoja de cálculo
            //     $sheet = $event->sheet;

            //     // Obtener el rango de celdas con datos
            //     $highestColumn = $sheet->getHighestColumn();
            //     $highestRow = $sheet->getHighestRow();
            //     $range = 'A1:'.$highestColumn.$highestRow;

            //     // Establecer el filtro automático en el rango de celdas
            //     $sheet->setAutoFilter($range);
            // },
        ];
    }
}
