<?php

namespace App\Http\Controllers;

use App\Repositories\ClientRepository;
use App\Repositories\InspectionRepository;
use App\Repositories\MaintenanceRepository;
use App\Repositories\UserRepository;
use App\Repositories\VehicleRepository;
use Illuminate\Http\Request;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(
        protected QueryController $queryController,
        protected UserRepository $userRepository,
        protected ClientRepository $clientRepository,
        protected VehicleRepository $vehicleRepository,
        protected InspectionRepository $inspectionRepository,
        protected MaintenanceRepository $maintenanceRepository,
    ) {}
    public function countAllData(Request $request)
    {
        try {
            $vehicleCount = $this->vehicleRepository->countData($request->all());
            $clientCount = $this->clientRepository->countData($request->all());
            $request['inspection_type_id'] = '1';
            $inspectionPreOperationalCount = $this->inspectionRepository->countData($request->all());
            $request['inspection_type_id'] = '2';
            $inspectionHSEQCount = $this->inspectionRepository->countData($request->all());
             $request['status'] = 'completed';
             $maintenanceCompletedCount = $this->maintenanceRepository->countData($request->all());
             $request['status'] = 'assigned';
            $maintenanceAssignedCount = $this->maintenanceRepository->countData($request->all());

            return response()->json([
                'code' => 200,
                'vehicleCount' => $vehicleCount,
                'clientCount' => $clientCount,
                'inspectionPreOperationalCount' => $inspectionPreOperationalCount,
                'inspectionHSEQCount' => $inspectionHSEQCount,
                'maintenanceCompletedCount' => $maintenanceCompletedCount,
                'maintenanceAssignedCount' => $maintenanceAssignedCount,

            ]);
        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function vehicleInfoForCompany(Request $request)
    {
        try {
            $data = $this->vehicleRepository->vehicleInfoForCompany($request->all());

            return response()->json([
                'code' => 200,
                'data' => $data,
            ]);
        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function vehicleInspectionsComparison(Request $request)
    {
        try {
            $inspection_count = $this->vehicleRepository->vehicleInspectionsComparison($request->all());



            return response()->json(['code' => 200, 'inspection_count' => $inspection_count]);
        } catch (Throwable $th) {
            return response()->json(['code' => 500, 'message' => $th->getMessage()]);
        }
    }

}
