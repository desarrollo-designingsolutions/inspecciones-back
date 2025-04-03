<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class InspectionListExport implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $data = collect($this->data)->map(function ($value) {
            return [
                'id' => $value->id,
                'vehicle_license_plate' => $value->vehicle?->license_plate,
                'inspection_date' => Carbon::parse($value->inspection_date)->format('d-m-Y'),
                'vehicle_brand_name' => $value->vehicle?->brand_vehicle?->name,
                'vehicle_model' => $value->vehicle?->model,
                'inspection_type_name' => $value->inspectionType?->name,
                'user_full_name' => $value->user_inspector?->full_name,
                'is_active' => $value->is_active ? 'Activo' : 'Inactivo',
            ];
        });

        return view('Exports.Inspection.InspectionListExportExcel', ['data' => $data]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Obtener el objeto hoja de cálculo
                $sheet = $event->sheet;

                // Obtener el rango de celdas con datos
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                $range = 'A1:'.$highestColumn.$highestRow;

                // Establecer el filtro automático en el rango de celdas
                $sheet->setAutoFilter($range);
            },
        ];
    }
}
