<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandVehicle\BrandVehicleSelectInfiniteResource;
use App\Http\Resources\Client\ClientSelectInfiniteResource;
use App\Http\Resources\Country\CountrySelectResource;
use App\Http\Resources\EmergencyElement\EmergencyElementSelectInfiniteResource;
use App\Http\Resources\TypeDocument\TypeDocumentSelectInfiniteResource;
use App\Http\Resources\TypeVehicle\TypeVehicleSelectInfiniteResource;
use App\Repositories\BrandVehicleRepository;
use App\Repositories\CityRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CountryRepository;
use App\Repositories\EmergencyElementRepository;
use App\Repositories\StateRepository;
use App\Repositories\TypeDocumentRepository;
use App\Repositories\TypeVehicleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function __construct(
        protected CountryRepository $countryRepository,
        protected StateRepository $stateRepository,
        protected CityRepository $cityRepository,
        protected UserRepository $userRepository,

        protected TypeVehicleRepository $typeVehicleRepository,
        protected BrandVehicleRepository $brandVehicleRepository,
        protected ClientRepository $clientRepository,
        protected TypeDocumentRepository $typeDocumentRepository,
        protected EmergencyElementRepository $emergencyElementRepository,
    ) {}

    public function selectInfiniteCountries(Request $request)
    {
        $countries = $this->countryRepository->list($request->all());

        $dataCountries = CountrySelectResource::collection($countries);

        return [
            'code' => 200,
            'countries_arrayInfo' => $dataCountries,
            'countries_countLinks' => $countries->lastPage(),
        ];
    }

    public function selectStates($country_id)
    {
        $states = $this->stateRepository->selectList($country_id);

        return [
            'code' => 200,
            'states' => $states,
        ];
    }

    public function selectCities($state_id)
    {
        $cities = $this->cityRepository->selectList($state_id);

        return [
            'code' => 200,
            'cities' => $cities,
        ];
    }

    public function selectCitiesCountry($country_id)
    {
        $country = $this->countryRepository->find($country_id, ['cities']);

        return response()->json([
            'code' => 200,
            'message' => 'Datos Encontrados',
            'cities' => $country['cities']->map(function ($item) {
                return [
                    'value' => $item->id,
                    'title' => $item->name,
                ];
            }),
        ]);
    }

    public function selectInfiniteTypeVehicle(Request $request)
    {
        $request['is_active'] = true;
        $typeVehicle = $this->typeVehicleRepository->list($request->all());
        $dataTypeVehicle = TypeVehicleSelectInfiniteResource::collection($typeVehicle);

        return [
            'code' => 200,
            'typeVehicle_arrayInfo' => $dataTypeVehicle,
            'typeVehicle_countLinks' => $typeVehicle->lastPage(),
        ];
    }

    public function selectInfiniteBrandVehicle(Request $request)
    {
        $request['is_active'] = true;
        $brandVehicle = $this->brandVehicleRepository->list($request->all());
        $dataBrandVehicle = BrandVehicleSelectInfiniteResource::collection($brandVehicle);

        return [
            'code' => 200,
            'brandVehicle_arrayInfo' => $dataBrandVehicle,
            'brandVehicle_countLinks' => $brandVehicle->lastPage(),
        ];
    }

    public function selectInfiniteClient(Request $request)
    {
        $request['is_active'] = true;
        $client = $this->clientRepository->list($request->all());
        $dataClient = ClientSelectInfiniteResource::collection($client);

        return [
            'code' => 200,
            'client_arrayInfo' => $dataClient,
            'client_countLinks' => $client->lastPage(),
        ];
    }

    public function selectInfiniteTypeDocument(Request $request)
    {
        $request['is_active'] = true;
        $typeDocument = $this->typeDocumentRepository->list($request->all());
        $dataTypeDocument = TypeDocumentSelectInfiniteResource::collection($typeDocument);

        return [
            'code' => 200,
            'typeDocument_arrayInfo' => $dataTypeDocument,
            'typeDocument_countLinks' => $typeDocument->lastPage(),
        ];
    }

    public function selectInfiniteEmergencyElement(Request $request)
    {
        $request['is_active'] = true;
        $emergencyElement = $this->emergencyElementRepository->list($request->all());
        $dataEmergencyElement = EmergencyElementSelectInfiniteResource::collection($emergencyElement);

        return [
            'code' => 200,
            'emergencyElement_arrayInfo' => $dataEmergencyElement,
            'emergencyElement_countLinks' => $emergencyElement->lastPage(),
        ];
    }
}
