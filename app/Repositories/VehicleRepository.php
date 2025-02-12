<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class VehicleRepository extends BaseRepository
{
    public function __construct(Vehicle $modelo)
    {
        parent::__construct($modelo);
    }

    public function list($request = [], $with = [], $select = ['*'])
    {
        $data = $this->model->select($select)->with($with)->where(function ($query) use ($request) {
            filterComponent($query, $request);

            if (! empty($request['company_id'])) {
                $query->where('company_id', $request['company_id']);
            }

            if (! empty($request['is_active'])) {
                $query->where('is_active', $request['is_active']);
            }
        })->where(function ($query) use ($request) {
            if (isset($request['searchQueryInfinite']) && ! empty($request['searchQueryInfinite'])) {
                $query->orWhere('name', 'like', '%' . $request['searchQueryInfinite'] . '%');
            }
        });

        if (isset($request['sortBy'])) {
            $sortBy = json_decode($request['sortBy'], 1);
            foreach ($sortBy as $key => $value) {
                $data = $data->orderBy($value['key'], $value['order']);
            }
        }

        if (empty($request['typeData'])) {
            $data = $data->paginate($request['perPage'] ?? Constants::ITEMS_PER_PAGE);
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public function store(array $request, $id = null)
    {
        $request = $this->clearNull($request);

        // Determinar el ID a utilizar para buscar o crear el modelo
        $idToUse = ($id === null || $id === 'null') && ! empty($request['id']) && $request['id'] !== 'null' ? $request['id'] : $id;

        if (! empty($idToUse)) {
            $data = $this->model->find($idToUse);
        } else {
            $data = $this->model::newModelInstance();
        }

        foreach ($request as $key => $value) {
            $data[$key] = is_array($request[$key]) ? $request[$key]['value'] : $request[$key];
        }

        $data->save();

        return $data;
    }

    public function selectList($request = [], $with = [], $select = [], $fieldValue = 'id', $fieldTitle = 'name')
    {
        $data = $this->model->with($with)->where(function ($query) use ($request) {
            if (! empty($request['idsAllowed'])) {
                $query->whereIn('id', $request['idsAllowed']);
            }
        })->get()->map(function ($value) use ($with, $select, $fieldValue, $fieldTitle) {
            $data = [
                'value' => $value->$fieldValue,
                'title' => $value->$fieldTitle,
            ];

            if (count($select) > 0) {
                foreach ($select as $s) {
                    $data[$s] = $value->$s;
                }
            }

            if (count($with) > 0) {
                foreach ($with as $s) {
                    $data[$s] = $value->$s;
                }
            }

            return $data;
        });

        return $data;
    }

    public function searchOne($request = [], $with = [], $select = ['*'])
    {
        $data = $this->model->select($select)->with($with)->where(function ($query) use ($request) {
            if (! empty($request['company_id'])) {
                $query->where('company_id', $request['company_id']);
            }
        });

        $data = $data->first();

        return $data;
    }

    public function countData($request = [])
    {
        $data = $this->model->where(function ($query) use ($request) {
            if (! empty($request['company_id'])) {
                $query->where('company_id', $request['company_id']);
                $query->where('is_active', true);
            }
        });

        $data = $data->count();

        return $data;
    }

    public function validateLicensePlate($request = []): bool
    {
        $data = $this->model
            ->where(function ($query) use ($request) {
                if (! empty($request['company_id'])) {
                    $query->where('company_id', $request['company_id']);
                }
                if (! empty($request['license_plate'])) {
                    $query->where('license_plate', $request['license_plate']);
                }
                if (! empty($request['id'])) {
                    $query->whereNot('id', $request['id']);
                }
            })->first();

        return $data !== null; // Retorna true si la licencia cumple con ambas condiciones
    }

    public function vehicleInfoForCompany($request)
    {
        // Validar las fechas de inicio y fin en la solicitud
        $startDate = isset($request['start_date']) ? $request['start_date'] : null;
        $endDate = isset($request['end_date']) ? $request['end_date'] : null;

        $today = now()->toDateString();

        // Obtener el tiempo total invertido en todas las tareas completadas de la empresa
        $query = $this->model
            ->where(function ($query) use ($request) {
                if (! empty($request['company_id'])) {
                    $query->where('company_id', $request['company_id']);
                    $query->where('is_active', 1);
                }
            })
            ->withCount([
                'inspection',
                'maintenance',
                'type_documents'  => function ($q) use ($today) {
                    $q->where('expiration_date', '>=', $today);
                },
                'emergency_elements' => function ($q) use ($today) {
                    $q->where('expiration_date', '>=', $today);
                },
            ])
            ->get();



        return $query;
    }

    public function vehicleInspectionsComparison($request)
    {
        $query = DB::table('vehicles as v')
            ->leftJoin('inspections as i', 'v.id', '=', 'i.vehicle_id')
            ->select(
                DB::raw("YEAR(i.created_at) as inspection_year"),
                DB::raw("MONTH(i.created_at) as inspection_month"),
                DB::raw("SUM(CASE WHEN i.inspection_type_id = 1 AND i.is_completed = 0 THEN 1 ELSE 0 END) as type1_incomplete"),
                DB::raw("SUM(CASE WHEN i.inspection_type_id = 1 AND i.is_completed = 1 THEN 1 ELSE 0 END) as type1_complete"),
                DB::raw("SUM(CASE WHEN i.inspection_type_id = 2 AND i.is_completed = 0 THEN 1 ELSE 0 END) as type2_incomplete"),
                DB::raw("SUM(CASE WHEN i.inspection_type_id = 2 AND i.is_completed = 1 THEN 1 ELSE 0 END) as type2_complete")
            )
            ->where('v.company_id', $request['company_id'])
            ->groupBy(DB::raw("YEAR(i.created_at)"), DB::raw("MONTH(i.created_at)"))
            ->orderBy(DB::raw("YEAR(i.created_at)"), 'desc')
            ->orderBy(DB::raw("MONTH(i.created_at)"), 'desc');

        // Aplicar filtros dinámicos
        $this->applyFilters($query, $request);

        return $query->get();
    }

    private function applyFilters($query, $request)
    {
        // Filtro por vehículo
        if (!empty($request['vehicle_id'])) {
            $query->where('v.id', $request['vehicle_id']);
        }

        // Filtro por mes en español
        if (!empty($request['month'])) {
            $monthNumber = $this->spanishMonthToNumber($request['month']);
            if ($monthNumber) {
                $query->where(DB::raw('MONTH(i.created_at)'), $monthNumber);
            }
        }

        // Filtro por año
        if (!empty($request['year'])) {
            $query->where(DB::raw('YEAR(i.created_at)'), $request['year']);
        }
    }

    private function spanishMonthToNumber($monthName)
    {
        $months = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12
        ];

        return $months[strtolower($monthName)] ?? null;
    }

    public function getInspectionFilters($companyId)
    {
        $inspections = DB::table('vehicles as v')
            ->join('inspections as i', 'v.id', '=', 'i.vehicle_id')
            ->select(
                DB::raw('YEAR(i.created_at) as year'),
                DB::raw('MONTH(i.created_at) as month')
            )
            ->where('v.company_id', $companyId)
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Procesar años
        $years = array_values(array_unique(array_map(function ($item) {
            return $item->year;
        }, $inspections->toArray())));

        rsort($years); // Orden descendente

        $months = array_values(array_unique(array_map(function ($item) {
            return $this->getMonthName($item->month);
        }, $inspections->toArray())));

        return [
            'years' => $years,
            'months' => $months,
        ];
    }

    private function getMonthName($monthNumber)
    {
        $months = [
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
            12 => 'Diciembre'
        ];

        return $months[$monthNumber] ?? 'Desconocido';
    }

    public function vehicleMaintenanceComparison($request)
    {
        $monthTranslations = [
            'Enero' => 'January',
            'Febrero' => 'February',
            'Marzo' => 'March',
            'Abril' => 'April',
            'Mayo' => 'May',
            'Junio' => 'June',
            'Julio' => 'July',
            'Agosto' => 'August',
            'Septiembre' => 'September',
            'Octubre' => 'October',
            'Noviembre' => 'November',
            'Diciembre' => 'December'
        ];

        // Traducir mes de entrada a inglés
        $requestMonth = $request['month'];
        $englishMonth = $monthTranslations[$requestMonth] ?? null;

        $monthNumber = date('m', strtotime($englishMonth));

        $vehicles = DB::table('vehicles as v')
            ->leftJoin('maintenances as i', 'v.id', '=', 'i.vehicle_id')
            ->select(
                'v.id as vehicle_id',
                'v.license_plate as vehicle_license_plate',

                DB::raw("SUM(CASE WHEN MONTH(i.created_at) = {$monthNumber} THEN 1 ELSE 0 END) as maintenances_in_month"),

                DB::raw("SUM(CASE WHEN i.id IS NOT NULL AND MONTH(i.created_at) <> {$monthNumber} THEN 1 ELSE 0 END) as maintenances_other"),

                DB::raw("COUNT(i.id) as total_maintenances")
            )
            ->where('v.company_id', $request['company_id'])
            ->when(!empty($request['vehicle_id']), function ($query) use ($request) {

                return $query->where('v.id', $request['vehicle_id']);
            })
            ->groupBy('v.id', 'v.license_plate')
            ->get();

        $monthsWithInspections = DB::table('maintenances as i')
            ->join('vehicles as v', 'i.vehicle_id', '=', 'v.id')
            ->where('v.company_id', $request['company_id'])
            ->selectRaw('DISTINCT MONTHNAME(i.created_at) as month_name, MONTH(i.created_at) as month_number')
            ->orderBy('month_number')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->month_name,
                    'number' => $item->month_number
                ];
            });

        return [
            'vehicles' => $vehicles,
            'available_months' => $monthsWithInspections,
        ];
    }
}
