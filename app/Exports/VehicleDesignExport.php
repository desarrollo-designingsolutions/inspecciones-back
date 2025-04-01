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

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        // Mapeo de nombres de meses en español a sus números correspondientes
        $monthNames = [
            'Enero' => 1,
            'Febrero' => 2,
            'Marzo' => 3,
            'Abril' => 4,
            'Mayo' => 5,
            'Junio' => 6,
            'Julio' => 7,
            'Agosto' => 8,
            'Septiembre' => 9,
            'Octubre' => 10,
            'Noviembre' => 11,
            'Diciembre' => 12,
        ];

        // Convertir el nombre del mes a su número correspondiente
        $monthName = $this->data['month'];
        $monthNumber = $monthNames[$monthName];
        $year = $this->data['year'];

        // Calcular los días del mes seleccionado
        $daysInMonth = Carbon::create($year, $monthNumber, 1)->daysInMonth;

        return view(
            'Exports.Vehicle.VehicleDesignExportExcel',
            [
                'data' => [
                    'license_plate' => $this->data['license_plate'],
                    'month' => $this->data['month'],
                    'year' => $this->data['year'],
                    'days' => $daysInMonth,
                ]
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
