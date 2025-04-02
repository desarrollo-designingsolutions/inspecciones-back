<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Models\Vehicle;
use App\QueryBuilder\Filters\DataSelectFilter;
use App\QueryBuilder\Filters\DateRangeFilter;
use App\QueryBuilder\Filters\QueryFilters;
use App\QueryBuilder\Sort\IsActiveSort;
use App\QueryBuilder\Sort\RelatedTableSort;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class VehicleRepository extends BaseRepository
{
    public function __construct(Vehicle $modelo)
    {
        parent::__construct($modelo);
    }

    public function paginate($request = [])
    {
        $cacheKey = $this->cacheService->generateKey("{$this->model->getTable()}_paginate", $request, 'string');

        return $this->cacheService->remember($cacheKey, function () use ($request) {
        $query = QueryBuilder::for($this->model->query())
            ->with(['type_vehicle:id,name', 'city:id,name'])
            ->select(['vehicles.id', 'license_plate', 'type_vehicle_id', 'date_registration', 'model', 'city_id', 'vehicles.is_active'])
            ->allowedFilters([
                'model',
                'is_active',
                AllowedFilter::callback('vehicles.id', new DataSelectFilter()),
                AllowedFilter::callback('date_registration', new DateRangeFilter()),
                AllowedFilter::callback('type_vehicle_id', new DataSelectFilter()),
                AllowedFilter::callback('inputGeneral', function ($queryX, $value) {
                    $queryX->where(function ($query) use ($value) {
                        $query->orWhere('license_plate', 'like', "%$value%");
                        $query->orWhere('model', 'like', "%$value%");

                        $query->orWhereHas('type_vehicle', function ($query) use ($value) {
                            $query->where('name', 'like', "%$value%");
                        });

                        $query->orWhereHas('city', function ($query) use ($value) {
                            $query->where('name', 'like', "%$value%");
                        });

                        QueryFilters::filterByDMYtoYMD($query, $value, 'date_registration');
                        QueryFilters::filterByText($query, $value, 'is_active', [
                            'activo' => 1,
                            'inactivo' => 0,
                        ]);
                    });
                }),
            ])
            ->defaultSort('license_plate')
            ->allowedSorts([
                'license_plate',
                'date_registration',
                'model',
                AllowedSort::custom('type_vehicle_name', new RelatedTableSort('vehicles', 'type_vehicles', 'name', 'type_vehicle_id')),
                AllowedSort::custom('city_name', new RelatedTableSort('vehicles', 'cities', 'name', 'city_id')),
                AllowedSort::custom('is_active', new IsActiveSort),
            ])->where(function ($query) use ($request) {
                if (!empty($request['company_id'])) {
                    $query->where('vehicles.company_id', $request['company_id']);
                }
            })
            ->paginate(request()->perPage ?? Constants::ITEMS_PER_PAGE);

        return $query;
        }, Constants::REDIS_TTL);
    }

    public function list($request = [], $with = [], $select = ['*'])
    {
        $data = $this->model->select($select)->with($with)->where(function ($query) use ($request) {
            filterComponent($query, $request);

            if (!empty($request['company_id'])) {
                $query->where('company_id', $request['company_id']);
            }

            if (!empty($request['is_active'])) {
                $query->where('is_active', $request['is_active']);
            }
        })->where(function ($query) use ($request) {
            if (isset($request['searchQueryInfinite']) && !empty($request['searchQueryInfinite'])) {
                $query->orWhere('license_plate', 'like', '%' . $request['searchQueryInfinite'] . '%');
            }
        })->orderBy('license_plate', 'asc');

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
        $idToUse = ($id === null || $id === 'null') && !empty($request['id']) && $request['id'] !== 'null' ? $request['id'] : $id;

        if (!empty($idToUse)) {
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
            if (!empty($request['idsAllowed'])) {
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
            if (!empty($request['company_id'])) {
                $query->where('company_id', $request['company_id']);
            }
        });

        $data = $data->first();

        return $data;
    }

    public function countData($request = [])
    {
        $data = $this->model->where(function ($query) use ($request) {
            if (!empty($request['company_id'])) {
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
                if (!empty($request['company_id'])) {
                    $query->where('company_id', $request['company_id']);
                }
                if (!empty($request['license_plate'])) {
                    $query->where('license_plate', $request['license_plate']);
                }
                if (!empty($request['id'])) {
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
                if (!empty($request['company_id'])) {
                    $query->where('company_id', $request['company_id']);
                    $query->where('is_active', 1);
                }
            })
            ->withCount([
                'inspection',
                'maintenance',
                'type_documents' => function ($q) use ($today) {
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
                DB::raw("SUM(CASE WHEN i.inspection_type_id = 1 THEN 1 ELSE 0 END) as type1"),
                DB::raw("SUM(CASE WHEN i.inspection_type_id = 2 THEN 1 ELSE 0 END) as type2"),
            )
            ->where('v.company_id', $request['company_id'])
            ->groupBy(DB::raw("YEAR(i.created_at)"), DB::raw("MONTH(i.created_at)"))
            ->orderBy(DB::raw("YEAR(i.created_at)"), 'desc')
            ->orderBy(DB::raw("MONTH(i.created_at)"), 'desc');

        // Aplicar filtros dinámicos
        $this->applyFilters($query, $request, 'i.created_at');

        return $query->get();
    }

    private function applyFilters($query, $request, $created_at)
    {
        // Filtro por vehículo
        if (!empty($request['vehicle_id'])) {
            $query->where('v.id', $request['vehicle_id']);
        }

        // Filtro por año
        if (!empty($request['year'])) {
            $query->where(DB::raw('YEAR(' . $created_at . ')'), $request['year']);
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
        $query = DB::table('vehicles as v')
            ->leftJoin('maintenances as m', 'v.id', '=', 'm.vehicle_id')
            ->select(
                DB::raw("YEAR(m.created_at) as maintenance_year"),
                DB::raw("MONTH(m.created_at) as maintenance_month"),
                DB::raw("SUM(CASE WHEN m.maintenance_type_id = 1 THEN 1 ELSE 0 END) as maintenance_count"),
            )
            ->where('v.company_id', $request['company_id'])
            ->groupBy(DB::raw("YEAR(m.created_at)"), DB::raw("MONTH(m.created_at)"))
            ->orderBy(DB::raw("YEAR(m.created_at)"), 'desc')
            ->orderBy(DB::raw("MONTH(m.created_at)"), 'asc');

        // Aplicar filtros dinámicos
        $this->applyFilters($query, $request, 'm.created_at');

        return $query->get();
    }
}
