<?php

namespace App\Http\Controllers\API;

use App\Events\ActivityServiceEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ActivityService;
use Validator;
use App\Http\Resources\ActivityServiceResource;
use App\Models\Activity;
use App\Models\Service;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class ActivityServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activitys = ActivityService::all();

        return $this->sendResponse(ActivityServiceResource::collection($activitys), 'ActivityService retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ActivityService::rules());
        if (isset($request->id)) {
            $query = ActivityService::find($request->id);
            $query->update(['remarks' => $request->remarks]);

            $channel = "activity-service-" . $request->activity_id;
            Event::dispatch(new ActivityServiceEvent(new ActivityServiceResource($query->first()), $channel));

            return $this->sendResponse(new ActivityServiceResource($query->first()), 'Activity Service updated successfully.');
        } else {
            $data = $request->only(['activity_id', 'service_id', 'amount', 'remarks']);

            $service_id = $data['service_id'];

            $service = Service::find($service_id);

            $activity = Activity::find($data['activity_id']);

            $data["commission_amount"] = 0;
            if ($service->is_comm_based == "Y" && $activity->is_dentist_required == 'Y') {
                if ($service->is_comm_fixed_amount == 'Y') {
                    $data["commission_amount"] = $service->comm_fixed_amount;
                } else {
                    $data["commission_amount"] = floatval($data["amount"] * ($service->commission_rate / 100));
                }
            }
            $data["is_delete"] = "N";
            $data["is_voided"] = "N";
            $data["added_by"] = Auth::id();
            $activity = ActivityService::create($data);

            $channel = "activity-service-" . $data['activity_id'];
            Event::dispatch(new ActivityServiceEvent(new ActivityServiceResource($activity), $channel));

            return $this->sendResponse(new ActivityServiceResource($activity), 'ActivityService created successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activity = ActivityService::find($id);

        if (is_null($activity)) {
            return $this->sendError('ActivityService not found.');
        }

        return $this->sendResponse(new ActivityServiceResource($activity), 'ActivityService retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ActivityService $activity)
    {

        $this->validate($request, ActivityService::rules());
        $input = $request->all();

        $activity->status = $input['status'];
        $activity->save();

        return $this->sendResponse(new ActivityServiceResource($activity), 'ActivityService updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $activity_service = ActivityService::find($id);
        $activity_id = $activity_service->activity_id;
        $activity_service->update(['is_delete' => 'Y']);

        $channel = "activity-service-" . $activity_id;
        Event::dispatch(new ActivityServiceEvent(new ActivityServiceResource($activity_service), $channel));

        return $this->sendResponse([], 'ActivityService deleted successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function void_activity_service(Request $request, ActivityService $activity_service)
    {

        $input = $request->all();

        $activity_service->is_voided = 'Y';
        $activity_service->voided_remarks = $input['voided_remarks'];
        $activity_service->save();

        $activity_id = $activity_service->activity_id;
        $channel = "activity-service-" . $activity_id;
        Event::dispatch(new ActivityServiceEvent(new ActivityServiceResource($activity_service), $channel));


        return $this->sendResponse(new ActivityServiceResource($activity_service), 'ActivityService updated successfully.');
    }


    public function commission_activity_service(Request $request, $id)
    {
        $activityService = ActivityService::find($id);

        if ($request->commission_type == 'Fixed') {
            $activityService->update(['is_commission_update' => 'Y', 'commission_amount' => $request->commission_amt, 'reason_to_update_commission' => $request->commission_remarks]);
        }

        if ($request->commission_type == 'Percentage') {
            $percentage = doubleval($request->commission_percentage) / 100;
            $computedAmt = doubleval($activityService->amount) * $percentage;
            $activityService->update(['is_commission_update' => 'Y', 'commission_amount' => $computedAmt, 'reason_to_update_commission' => $request->commission_remarks]);
        }
        $activity_id = $activityService->activity_id;
        $channel = "activity-service-" . $activity_id;
        Event::dispatch(new ActivityServiceEvent(new ActivityServiceResource($activityService), $channel));
        return $this->sendResponse(new ActivityServiceResource($activityService), 'Activity Service Commission was updated successfully.');
    }

    public function services_by_activity(Activity $activity)
    {
        $services = ActivityService::leftJoin('users', 'users.id', 'activity_services.added_by')
            ->where('activity_id', $activity->id)
            ->selectRaw('activity_services.*, users.name as added_by')
            ->get();
        return $this->sendResponse(ActivityServiceResource::collection($services), 'ActivityService retrieved successfully. ');
    }
}
