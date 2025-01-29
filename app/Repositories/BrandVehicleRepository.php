<?php

namespace App\Repositories;

use App\Models\BrandVehicle;

class BrandVehicleRepository extends BaseRepository
{
    public function __construct(BrandVehicle $modelo)
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
            $data = $data->paginate($request['perPage'] ?? 1);
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
        });

        $data = $data->count();

        return $data;
    }
}
