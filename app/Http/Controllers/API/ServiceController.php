<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use App\Models\UserBranch;
use Illuminate\Support\Facades\{Validator, Hash, Auth};

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth::user();
        $branch_id = $user->branch_id;

        $patients = Service::where(['branch_id' => $branch_id, 'is_deleted' => 'N'])->get();

        return $this->sendResponse(ServiceResource::collection($patients), 'Services retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, Service::rules());

        $user = Auth::user();
        $branch_id = $request->branch_id ?: $user->branch_id;

        $data = $request->all();

        if ($data["is_comm_fixed_amount"] == "Y") {
            $data["commission_rate"] = 0;
        }

        $service = Service::where(['branch_id' => $branch_id, 'name' => $request->name])->count();
        if ($service) {
            return $this->sendError('Oops! Service already exist.');
        }
        if ($data["is_comm_fixed_amount"] == "Y") {
            $data['commission_rate'] = 0;
            $data['comm_fixed_amount'] = $data["comm_fixed_amount"];
        } else {
            $data['comm_fixed_amount'] = 0;
        }

        $service = Service::create($data);

        return $this->sendResponse(new ServiceResource($service), 'Service created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patient = Service::find($id);

        if (is_null($patient)) {
            return $this->sendError('Service not found.');
        }

        return $this->sendResponse(new ServiceResource($patient), 'Service retrieved successfully.');
    }

    public function branch_services(Request $request)
    {

        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();

        $isBranchExistOnUser = UserBranch::where(['user_id' => Auth::id(), 'branch_id' => $request->branchId])->count();

        $services = [];
        if ($isBranchExistOnUser) {
            $query = Service::where(['branch_id' => $request->branchId, 'is_deleted' => 'N']);
            $services = $query->orderBy('name', 'asc')->get();
        }
        return $this->sendResponse(ServiceResource::collection($services), 'Services retrieved successfully.');
    }

    public function branch_services_paginated(Request $request)
    {
        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();

        // $isBranchExistOnUser = UserBranch::where(['user_id' => Auth::id(), 'branch_id' => $request->branchId])->count();
        $query = Service::select('*');

        $query->where(['is_deleted' => 'N']);

        if (isset($request->branchId)) {
            $query->where('branch_id', $request->branchId);
        }

        if (isset($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
            $query->orWhere('description', 'like', '%' . $request->search . '%');
        }
        $services = $query->orderBy('name', 'asc')->paginate(10);
        return $this->sendResponse($services, 'Services retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {

        $this->validate($request, Service::rules());
        $input = $request->all();

        $service->name = $input['name'];
        $service->description = $input['description'];
        $service->is_other = $input['is_other'];
        $service->is_comm_based = $input['is_comm_based'];
        $service->commission_rate = $input['commission_rate'];
        if ($input["is_comm_fixed_amount"] == 'Y') {
            $service->is_comm_fixed_amount = 'Y';
            $service->commission_rate = 0;
            $service->comm_fixed_amount = $input["comm_fixed_amount"];
        } else {
            $service->is_comm_fixed_amount = 'N';
            $service->comm_fixed_amount = 0;
        }
        $service->save();

        return $this->sendResponse(new ServiceResource($service), 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Service::where(['id' => $id])->update(['is_deleted' => 'Y']);
        // $patient->delete();

        return $this->sendResponse([], 'Service deleted successfully.');
    }

    public function other_branch_services(Request $request)
    {
        $services = Service::where('branch_id', $request->branchId)
            ->select('id', 'name', 'description', 'is_other', 'is_comm_based', 'commission_rate', 'is_comm_fixed_amount', 'comm_fixed_amount')
            ->get();

        return $this->sendResponse(ServiceResource::collection($services), 'Services retrieved successfully.');
    }

    public function copy_other_branch_services(Request $request)
    {
        $payload = [];
        foreach ($request->data as $key => $value) {
            $service = Service::where(['branch_id' => $request->branch_id, 'name' => $value['name']])->count();
            if (!$service) {
                $dataLoad = [];
                $dataLoad['name'] = $value['name'];
                $dataLoad['description'] = $value['description'];
                $dataLoad['is_other'] = $value['is_other'];
                $dataLoad['is_comm_based'] = $value['is_comm_based'];
                $dataLoad['commission_rate'] = $value['commission_rate'];
                $dataLoad['is_comm_fixed_amount'] = $value['is_comm_fixed_amount'];
                $dataLoad['comm_fixed_amount'] = $value['comm_fixed_amount'];
                $dataLoad['branch_id'] =  $request->branch_id;
                $dataLoad['created_at'] =  now();
                $payload[] = $dataLoad;
            }
        }
        if (count($payload)) {
            Service::insert($payload);
        }
        return $this->sendResponse([], 'Other Branch Service was copied successfully.');
    }
}
