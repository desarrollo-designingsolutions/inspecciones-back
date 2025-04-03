<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Models\Company;
use App\QueryBuilder\Filters\QueryFilters;
use App\QueryBuilder\Sort\IsActiveSort;
use App\QueryBuilder\Sort\RelatedTableSort;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyRepository extends BaseRepository
{
    public function __construct(Company $modelo)
    {
        parent::__construct($modelo);
    }

    public function paginate($request = [])
    {
        $cacheKey = $this->cacheService->generateKey("{$this->model->getTable()}_paginate", $request, 'string');

        return $this->cacheService->remember($cacheKey, function () {
            $query = QueryBuilder::for($this->model->query())
                ->with(['country:id,name', 'state:id,name', 'city:id,name'])
                ->select(['companies.id', 'logo', 'companies.name', 'nit', 'address', 'phone', 'email', 'companies.is_active', 'companies.created_at', 'final_date', 'companies.country_id', 'companies.state_id', 'city_id'])
                ->allowedFilters(filters: [
                    'is_active',
                    AllowedFilter::callback('inputGeneral', function ($query, $value) {
                        $query->orWhere('name', 'like', "%$value%");
                        $query->orWhere('nit', 'like', "%$value%");
                        $query->orWhere('phone', 'like', "%$value%");

                        $query->orWhereHas('country', function ($query) use ($value) {
                            $query->where('name', 'like', "%$value%");
                        });

                        $query->orWhereHas('state', function ($query) use ($value) {
                            $query->where('name', 'like', "%$value%");
                        });

                        $query->orWhereHas('city', function ($query) use ($value) {
                            $query->where('name', 'like', "%$value%");
                        });

                        QueryFilters::filterByText($query, $value, 'is_active', [
                            'activo' => 1,
                            'inactivo' => 0,
                        ]);
                    }),
                ])
                ->allowedSorts([
                    'name',
                    'nit',
                    'phone',
                    AllowedSort::custom('country', new RelatedTableSort('companies', 'countries', 'name', 'country_id')),
                    AllowedSort::custom('state', new RelatedTableSort('companies', 'states', 'name', 'state_id')),
                    AllowedSort::custom('city', new RelatedTableSort('companies', 'cities', 'name', 'city_id')),
                    AllowedSort::custom('is_active', new IsActiveSort),
                ])
                ->paginate(request()->perPage ?? Constants::ITEMS_PER_PAGE);

            return $query;
        }, Constants::REDIS_TTL);
    }

    public function list($request = [], $with = [], $select = ['*'], $idsAllowed = [], $idsNotAllowed = [])
    {
        $data = $this->model->select($select)
            ->with($with)
            ->where(function ($query) use ($request) {
                filterComponent($query, $request);

                if (! empty($request['name'])) {
                    $query->where('name', 'like', '%'.$request['name'].'%');
                }
            })
            ->where(function ($query) use ($request) {
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
}
