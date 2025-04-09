<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VehicleListExport implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $items = $this->data->items(); // <- extrae solo los vehículos
        $data = collect($items)->map(function ($value) {
            return [
                'id' => $value->id,
                'name' => $value->name,
                'license_plate' => $value->license_plate,
                'type_vehicle_name' => $value->type_vehicle?->name,
                'date_registration' => $value->date_registration,
                'model' => $value->model,
                'city_name' => $value->city?->name,
                'is_active' => $value->is_active ? 'Activo' : 'Inactivo',
            ];
        });

        return view('Exports.Vehicle.VehicleListExportExcel', ['data' => $data]);
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
