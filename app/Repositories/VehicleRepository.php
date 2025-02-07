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
            ->leftJoin('inspections as i', 'v.id', '=', 'i.vehicle_id')
            ->select(
                'v.id as vehicle_id',
                'v.license_plate as vehicle_license_plate',

                DB::raw("SUM(CASE WHEN MONTH(i.created_at) = {$monthNumber} THEN 1 ELSE 0 END) as inspections_in_month"),

                DB::raw("SUM(CASE WHEN i.id IS NOT NULL AND MONTH(i.created_at) <> {$monthNumber} THEN 1 ELSE 0 END) as inspections_other"),

                DB::raw("COUNT(i.id) as total_inspections")
            )
            ->where('v.company_id', $request['company_id'])
            ->when(!empty($request['vehicle_id']), function ($query) use ($request) {

                return $query->where('v.id', $request['vehicle_id']);
            })
            ->groupBy('v.id', 'v.license_plate')
            ->get();

        $monthsWithInspections = DB::table('inspections as i')
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
