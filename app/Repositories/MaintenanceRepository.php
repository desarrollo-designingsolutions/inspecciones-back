<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Models\Maintenance;
use App\QueryBuilder\Filters\DataSelectFilter;
use App\QueryBuilder\Filters\DataSelectRelationFilter;
use App\QueryBuilder\Filters\DateRangeFilter;
use App\QueryBuilder\Filters\QueryFilters;
use App\QueryBuilder\Sort\DynamicJoinConcatSort;
use App\QueryBuilder\Sort\RelatedTableSort;
use App\QueryBuilder\Sort\StatusOldSort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MaintenanceRepository extends BaseRepository
{
    public function __construct(Maintenance $modelo)
    {
        parent::__construct($modelo);
    }

    public function paginate($request = [])
    {
        $customTypes = [
            ['value' => 'unassigned', 'title' => 'Sin asignar'],
            ['value' => 'assigned', 'title' => 'Asignado'],
            ['value' => 'canceled', 'title' => 'Cancelado'],
            ['value' => 'completed', 'title' => 'Completado'],
        ];

        $cacheKey = $this->cacheService->generateKey("{$this->model->getTable()}_paginate", $request, 'string');

        // return $this->cacheService->remember($cacheKey, function () use ($request, $customTypes) {
        $query = QueryBuilder::for($this->model->query())
            ->with(['user_inspector:id,name,surname', 'user_mechanic:id,name,surname', 'vehicle:id,license_plate,model,brand_vehicle_id', 'vehicle.brand_vehicle:id,name', 'maintenanceType:id,name'])
            ->select(['maintenances.id', 'maintenance_date', 'vehicle_id', 'user_inspector_id', 'user_mechanic_id', 'maintenances.company_id', 'status', 'maintenance_type_id'])
            ->allowedFilters([
                AllowedFilter::callback('maintenance_date', new DateRangeFilter),
                AllowedFilter::callback('vehicle_id', new DataSelectFilter),
                AllowedFilter::callback('vehicle.brand_vehicle_id', new DataSelectRelationFilter('vehicle', 'brand_vehicle_id')),
                AllowedFilter::callback('vehicle.model', new DataSelectRelationFilter('vehicle', 'model')),
                AllowedFilter::callback('user_inspector.id', new DataSelectRelationFilter('user_inspector', 'id')),
                AllowedFilter::callback('user_mechanic.id', new DataSelectRelationFilter('user_mechanic', 'id')),
                AllowedFilter::callback('status', new DataSelectFilter),
                AllowedFilter::callback('inputGeneral', function ($queryFilter, $value) use ($customTypes) {
                    $queryFilter->where(function ($query) use ($value, $customTypes) {
                        $query->orWhereHas('vehicle', function ($subQuery) use ($value) {
                            $subQuery->where('license_plate', 'like', "%$value%");
                            $subQuery->orWhere('model', 'like', "%$value%");
                        });

                        $query->orWhereHas('vehicle.brand_vehicle', function ($subQuery) use ($value) {
                            $subQuery->where('name', 'like', "%$value%");
                        });

                        $query->orWhereHas('user_inspector', function ($subquery) use ($value) {
                            $subquery->whereRaw("CONCAT(users.name, ' ', users.surname) LIKE ?", ["%{$value}%"]);
                        });

                        $query->orWhereHas('user_mechanic', function ($subquery) use ($value) {
                            $subquery->whereRaw("CONCAT(users.name, ' ', users.surname) LIKE ?", ["%{$value}%"]);
                        });

                        QueryFilters::filterByDMYtoYMD($query, $value, 'maintenance_date');
                        QueryFilters::filterByStatusOld($query, $value, 'status', $customTypes);
                    });
                }),
            ])
            ->allowedSorts([
                'maintenance_date',
                AllowedSort::custom('vehicle_license_plate', new RelatedTableSort('maintenances', 'vehicles', 'license_plate', 'vehicle_id')),
                AllowedSort::custom('vehicle_brand_name', new class implements \Spatie\QueryBuilder\Sorts\Sort
                {
                    public function __invoke($query, $descending, string $property)
                    {
                        $direction = $descending ? 'desc' : 'asc';

                        $query->join('vehicles', 'maintenances.vehicle_id', '=', 'vehicles.id')
                            ->join('brand_vehicles', 'vehicles.brand_vehicle_id', '=', 'brand_vehicles.id')
                            ->orderBy('brand_vehicles.name', $direction);
                    }
                }),
                AllowedSort::custom('vehicle_model', new RelatedTableSort('maintenances', 'vehicles', 'model', 'vehicle_id')),
                AllowedSort::custom('user_inspector_full_name', new DynamicJoinConcatSort(
                    concat: "users.name, ' ', users.surname",
                    relatedTable: 'users',
                    foreignKey: 'user_inspector_id'
                )),
                AllowedSort::custom('user_mechanic_full_name', new DynamicJoinConcatSort(
                    concat: "users.name, ' ', users.surname",
                    relatedTable: 'users',
                    foreignKey: 'user_mechanic_id'
                )),
                AllowedSort::custom('status', new StatusOldSort($customTypes)),
                ])->where(function ($query) use ($request) {
                    if (! empty($request['company_id'])) {
                        $query->where('maintenances.company_id', $request['company_id']);
                    }
                });

        if (empty($request['typeData'])) {
            $query = $query->paginate(request()->perPage ?? Constants::ITEMS_PER_PAGE);
        } else {
            $query = $query->get();
        }

        return $query;
        // }, Constants::REDIS_TTL);
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

            if (! empty($request['user_mechanic_id'])) {
                $query->where('user_mechanic_id', $request['user_mechanic_id']);
            }

            if (isset($request['searchQuery']['relationsGeneral']) && count($request['searchQuery']['relationsGeneral']) > 0) {

                $search = $request['searchQuery']['generalSearch'];

                // Recursivamente filtrar todos los elementos que contienen '|custom'
                $customColumns = [];

                array_walk_recursive($request['searchQuery']['relationsGeneral'], function ($value) use (&$customColumns) {
                    // Verificar si el valor contiene '|custom'
                    if (strpos($value, '|custom') !== false) {
                        // Eliminar '|custom' y agregar el valor al array
                        $customColumns[] = str_replace('|custom', '', $value);
                    }
                });

                foreach ($customColumns as $key => $value) {
                    if ($value == 'status' && ! empty($search)) {
                        $status = getResponseStatus($search, 'title', 'value', 'LIKE');
                        if (isset($status) && is_string($status)) {
                            $query->orWhere('status', $status);
                        }
                    }
                }
            }

        })->where(function ($query) use ($request) {
            if (isset($request['searchQueryInfinite']) && ! empty($request['searchQueryInfinite'])) {
                $query->orWhere('name', 'like', '%'.$request['searchQueryInfinite'].'%');
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
            }
            if (! empty($request['status'])) {
                $query->where('status', $request['status']);
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
            })->first();

        return $data !== null; // Retorna true si la licencia cumple con ambas condiciones
    }
}
