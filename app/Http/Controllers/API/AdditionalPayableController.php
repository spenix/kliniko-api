<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdditionalPayableResource;
use App\Models\Activity;
use App\Models\AdditionalPayable;
use Illuminate\Http\Request;

class AdditionalPayableController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, AdditionalPayable::rules());

        $data = $request->all();

        $additional_payables = AdditionalPayable::create($data);
     
        return $this->sendResponse(new AdditionalPayableResource($additional_payables), 'Additional payable created successfully.');
    }

    public function get_additional_payable_by_activity(Activity $activity) {
        
        $additional_payables = AdditionalPayable::where('activity_id', $activity->id)
                                ->get();

        return $this->sendResponse(AdditionalPayableResource::collection($additional_payables), 'Additional payables retrieved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $additional_payable = AdditionalPayable::find($id);
        $additional_payable->update(['is_delete' => 'Y']);

        return $this->sendResponse([], 'ActivityService deleted successfully.');
    }
}
