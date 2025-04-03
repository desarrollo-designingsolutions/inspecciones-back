<?php

namespace App\Http\Controllers;

use App\Repositories\ClientRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\MaintenanceRepository;
use App\Repositories\UserRepository;
use App\Repositories\VehicleRepository;
use Illuminate\Http\Request;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        protected QueryController $queryController,
        protected UserRepository $userRepository,
        protected ClientRepository $clientRepository,
        protected VehicleRepository $vehicleRepository,
        protected InspectionRepository $inspectionRepository,
        protected MaintenanceRepository $maintenanceRepository,
    ) {}

    public function countAllData(Request $request)
    {
        try {
            $vehicleCount = $this->vehicleRepository->countData($request->all());
            $clientCount = $this->clientRepository->countData($request->all());
            $request['inspection_type_id'] = '1';
            $inspectionPreOperationalCount = $this->inspectionRepository->countData($request->all());
            $request['inspection_type_id'] = '2';
            $inspectionHSEQCount = $this->inspectionRepository->countData($request->all());
            $request['status'] = 'completed';
            $maintenanceCompletedCount = $this->maintenanceRepository->countData($request->all());
            $request['status'] = 'assigned';
            $maintenanceAssignedCount = $this->maintenanceRepository->countData($request->all());

            return response()->json([
                'code' => 200,
                'vehicleCount' => $vehicleCount,
                'clientCount' => $clientCount,
                'inspectionPreOperationalCount' => $inspectionPreOperationalCount,
                'inspectionHSEQCount' => $inspectionHSEQCount,
                'maintenanceCompletedCount' => $maintenanceCompletedCount,
                'maintenanceAssignedCount' => $maintenanceAssignedCount,

            ]);
        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function vehicleInfoForCompany(Request $request)
    {
        try {
            $data = $this->vehicleRepository->vehicleInfoForCompany($request->all());

            return response()->json([
                'code' => 200,
                'data' => $data,
            ]);
        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function vehicleInspectionsComparison(Request $request)
    {
        try {
            $data = $this->vehicleRepository->vehicleInspectionsComparison($request->all());

            $yearsAndMounts = $this->vehicleRepository->getInspectionFilters($request->input('company_id'));

            $datasets = [];

            $years = $yearsAndMounts['years'];

            $months = $yearsAndMounts['months'];

            // 1. Agrupar datos por mes
            $monthsData = collect($data)->groupBy('inspection_month');

            // 2. Obtener meses únicos ORDENADOS numéricamente
            $uniqueMonths = $monthsData->keys()
                ->sort(SORT_NUMERIC)
                ->values();

            // 3. Crear labels con nombres de mes
            $labels = $uniqueMonths->map(function ($month) {
                return $this->getMonthName($month);
            })->toArray();

            // 4. Tipos de inspección
            $types = [
                'type1' => 'Pre Operacional',
                'type2' => 'HSEQ',
            ];

            // 5. Colores para cada tipo
            $colors = ['#FF6384', '#36A2EB', '#4BC0C0', '#FFCE56'];
            $typeIndex = 0;

            foreach ($types as $typeKey => $typeLabel) {
                $dataset = [
                    'label' => $typeLabel,
                    'data' => [],
                    'backgroundColor' => $colors[$typeIndex],
                    'borderColor' => $colors[$typeIndex],
                ];

                // Llenar datos para cada mes
                foreach ($uniqueMonths as $month) {
                    $monthData = $monthsData->get($month)?->first();
                    $dataset['data'][] = $monthData ? $monthData->{$typeKey} : 0;
                }

                $datasets[] = $dataset;
                $typeIndex++;
            }

            return response()->json([
                'code' => 200,
                'labels' => $labels,
                'datasets' => $datasets,
                'years' => $years,
                'months' => $months,
            ]);

        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    private function getMonthName($monthNumber)
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return $months[$monthNumber] ?? 'Desconocido';
    }

    public function vehicleMaintenanceComparison(Request $request)
    {
        try {
            $data = $this->vehicleRepository->vehicleMaintenanceComparison($request->all());

            $yearsAndMounts = $this->vehicleRepository->getInspectionFilters($request->input('company_id'));

            $datasets = [];

            $years = $yearsAndMounts['years'];

            $months = $yearsAndMounts['months'];

            // 1. Agrupar datos por mes
            $monthsData = collect($data)->groupBy('maintenance_month');

            // 2. Obtener meses únicos ORDENADOS numéricamente
            $uniqueMonths = $monthsData->keys()
                ->sort(SORT_NUMERIC)
                ->values();

            // 3. Crear labels con nombres de mes
            $labels = $uniqueMonths->map(function ($month) {
                return $this->getMonthName($month);
            })->toArray();

            // 5. Colores para cada tipo
            $colors = ['#FF6384', '#36A2EB', '#4BC0C0', '#FFCE56'];
            $typeIndex = 0;

            foreach ($labels as $typeKey => $typeLabel) {
                $dataset = [
                    'label' => $typeLabel,
                    'data' => array_fill(0, count($labels), 0),
                    'backgroundColor' => $colors[$typeIndex],
                    'borderColor' => $colors[$typeIndex],
                ];

                $dataset['data'][$typeKey] = $data[$typeKey]->maintenance_count;
                // // Llenar datos para cada mes
                // foreach ($uniqueMonths as $month) {
                //      $monthData = $monthsData->get($month)?->first();
                //      $dataset['data'][] = $monthData ? $monthData->maintenance_count : 0;
                // }

                $datasets[] = $dataset;
                $typeIndex++;
            }

            return response()->json([
                'code' => 200,
                'labels' => $labels,
                'datasets' => $datasets,
                'years' => $years,
                'months' => $months,
            ]);

        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }
}
