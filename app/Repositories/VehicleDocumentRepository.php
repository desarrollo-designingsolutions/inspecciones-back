<?php

namespace App\Repositories;

use App\Helpers\Constants;
use App\Models\VehicleDocument;

class VehicleDocumentRepository extends BaseRepository
{
    public function __construct(VehicleDocument $modelo)
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
                // var_dump($request['is_active']);
                $query->where('is_active', $request['is_active']);
            }
        })->where(function ($query) use ($request) {
            if (isset($request['searchQueryGeneral']) && ! empty($request['searchQueryGeneral'])) {
                $query->Where('name', 'like', '%' . $request['searchQueryGeneral'] . '%');
            }
            if (isset($request['searchQueryArray']) && ! empty($request['searchQueryArray'])) {
                $query->where(function ($subQuery) use ($request) {
                    foreach ($request['searchQueryArray'] as $item) {
                        if (isset($item['search'])) {
                            $subQuery->orWhere('is_active', 'like', '%' . $item['search'] . '%');
                        }
                    }
                });
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

    public function store($request)
    {
        $request = $this->clearNull($request);

        if (! empty($request['id'])) {
            $data = $this->model->find($request['id']);
        } else {
            $data = $this->model::newModelInstance();
        }

        foreach ($request as $key => $value) {
            $data[$key] = is_array($request[$key]) ? $request[$key]['value'] : $request[$key];
        }
        $data->save();

        return $data;
    }

    public function deleteArray($arrayIds, $type_document_id)
    {
        $data = $this->model->whereNotIn('id', $arrayIds)->where('type_document_id', $type_document_id)->delete();

        return $data;
    }
}
