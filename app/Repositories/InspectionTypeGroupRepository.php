<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Models\InspectionType;
use App\Models\InspectionTypeGroup;

class InspectionTypeGroupRepository extends BaseRepository
{
    public function __construct(InspectionTypeGroup $modelo)
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

            if (! empty($request['inspection_type_id'])) {
                $query->where('inspection_type_id', $request['inspection_type_id']);
            }

            if (! empty($request['is_active'])) {
                $query->where('is_active', $request['is_active']);
            }

            if (! empty($request['ids'])) {
                $query->whereIn('id', $request['ids']);
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

    public function typeInspectionInputs()
    {
        $inspectionTypes = InspectionType::pluck('name', 'id');
        $grouped = $this->model->select('id', 'inspection_type_id', 'name')->get()->groupBy('inspection_type_id');

        $transformed = $grouped->mapWithKeys(function ($items, $key) use ($inspectionTypes) {
            $typeName = $inspectionTypes[$key];

            return ['type_inspection_'.$key => [
                'name' => $typeName,
                'inputs' => $items->map(function ($model) {
                    return [
                        'id' => $model->id,
                        'description' => $model->name,
                        'check_state' => false,
                    ];
                })->all(),
            ]];
        });

        return $transformed;
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
