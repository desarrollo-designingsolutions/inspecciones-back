<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Models\Inspection;
use App\QueryBuilder\Filters\DataSelectFilter;
use App\QueryBuilder\Filters\DataSelectRelationFilter;
use App\QueryBuilder\Filters\QueryFilters;
use App\QueryBuilder\Sort\DynamicConcatSort;
use App\QueryBuilder\Sort\IsActiveSort;
use App\QueryBuilder\Sort\RelatedTableSort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class InspectionRepository extends BaseRepository
{
    public function __construct(Inspection $modelo)
    {
        parent::__construct($modelo);
    }

    public function paginate($request = [])
    {
        $cacheKey = $this->cacheService->generateKey("{$this->model->getTable()}_paginate", $request, 'string');
        return $this->cacheService->remember($cacheKey, function () use ($request) {
            $query = QueryBuilder::for(subject: $this->model->query())
                ->with(['vehicle:id,license_plate,brand_vehicle_id,model', 'brand_vehicle', 'vehicle.brand_vehicle:id,name', 'user_inspector:id,name,surname', 'inspectionType:id,name'])
                ->select(['inspections.id', 'vehicle_id', 'inspection_date', 'inspection_type_id', 'inspection_type_id', 'user_inspector_id', 'inspections.is_active'])
                ->join('users', 'users.id', '=', 'user_inspector_id')
                ->allowedFilters([
                    'inspection_date',
                    'inspectionType.name',
                    'is_active',
                    AllowedFilter::callback('vehicle_id', new DataSelectFilter()),

                    AllowedFilter::callback('vehicle.brand_vehicle', new DataSelectRelationFilter()),

                    AllowedFilter::callback('vehicle.model', new DataSelectRelationFilter("vehicle", "model")),

                    AllowedFilter::callback('user_inspector_id', new DataSelectFilter()),

                    AllowedFilter::callback('inputGeneral', function ($queryX, $value) {
                        $queryX->where(function ($query) use ($value) {
                            $query->orWhereHas('vehicle', function ($subQuery) use ($value) {
                                $subQuery->where('license_plate', 'like', "%$value%")->orWhere('model', 'like', "%$value%");
                            });

                            $query->orWhereHas('vehicle.brand_vehicle', function ($subQuery) use ($value) {
                                $subQuery->where('name', 'like', "%$value%");
                            });

                            $query->orWhereHas('inspectionType', function ($subQuery) use ($value) {
                                $subQuery->where('name', 'like', "%$value%");
                            });

                            $query->orWhereHas('user_inspector', function ($subQuery) use ($value) {
                                $subQuery->whereRaw("CONCAT(users.name, ' ', users.surname) LIKE ?", ["%{$value}%"]);
                            });

                            QueryFilters::filterByDMYtoYMD($query, $value, 'inspection_date');
                            QueryFilters::filterByText($query, $value, 'inspections.is_active', [
                                'activo' => 1,
                                'inactivo' => 0,
                            ]);
                        });
                    }),
                ])
                ->allowedSorts([
                    'inspection_date',
                    AllowedSort::custom('vehicle_license_plate', new RelatedTableSort('inspections', 'vehicles', 'license_plate', 'vehicle_id')),
                    AllowedSort::custom('vehicle_brand_name', new class implements \Spatie\QueryBuilder\Sorts\Sort {
                public function __invoke($query, $descending, string $property)
                {
                    $direction = $descending ? 'desc' : 'asc';

                    $query->join('vehicles', 'inspections.vehicle_id', '=', 'vehicles.id')
                        ->join('brand_vehicles', 'vehicles.brand_vehicle_id', '=', 'brand_vehicles.id')
                        ->orderBy('brand_vehicles.name', $direction);
                }
                    }),
                    AllowedSort::custom('vehicle_model', new RelatedTableSort('inspections', 'vehicles', 'model', 'vehicle_id')),
                    AllowedSort::custom('inspection_type_name', new RelatedTableSort('inspections', 'inspection_types', 'name', 'inspection_type_id')),
                    AllowedSort::custom('user_inspector_full_name', new DynamicConcatSort("users.name, ' ', users.surname")),
                    AllowedSort::custom('is_active', new IsActiveSort),
                ])
                ->where(function ($query) use ($request) {
                    if (!empty($request['company_id'])) {
                        $query->where('inspections.company_id', $request['company_id']);
                    }
                });

            if (empty($request['typeData'])) {
                $query = $query->paginate(request()->perPage ?? Constants::ITEMS_PER_PAGE);
            } else {
                $query = $query->get();
            }

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
            if (!empty($request['inspection_type_id'])) {
                $query->where('inspection_type_id', $request['inspection_type_id']);
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
            })->first();

        return $data !== null; // Retorna true si la licencia cumple con ambas condiciones
    }
}
