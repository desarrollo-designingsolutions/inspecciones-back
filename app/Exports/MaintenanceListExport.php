<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MaintenanceListExport implements FromView, ShouldAutoSize, WithEvents
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
            $mechanic_name = User::find($value->user_mechanic_id);
            $mechanic_name = $mechanic_name?->full_name;

            $inspector_name = User::find($value->user_inspector_id);
            $inspector_name = $inspector_name?->full_name;

            return [
                'id' => $value->id,
                'vehicle_license_plate' => $value->vehicle?->license_plate,
                'maintenance_date' => Carbon::parse($value->maintenance_date)->format('d-m-Y'),
                'vehicle_brand_name' => $value->vehicle?->brand_vehicle?->name,
                'vehicle_model' => $value->vehicle?->model,
                'maintenance_type_id' => $value->maintenance_type_id,
                'maintenance_type_name' => $value->maintenanceType?->name,
                'user_inspector_full_name' => $inspector_name,
                'user_mechanic_full_name' => $mechanic_name,
                'status' => getResponseStatus($value->status),
            ];
        });

        return view('Exports.Maintenance.MaintenanceListExportExcel', ['data' => $data]);
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
