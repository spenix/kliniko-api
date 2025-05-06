<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityRecommendationResource;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ActivityRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityRecommendations extends BaseController
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
        $this->validate($request, ActivityRecommendation::rules());
        $payload = $request->only(['activity_id', 'patient_id', 'treatment', 'next_visit_recom']);
        $payload['created_by'] = Auth::id();
        $activityRecom = ActivityRecommendation::create($payload);
        return $this->sendResponse(new ActivityRecommendationResource($activityRecom), 'Recommendation was successfully saved.');
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
        $qry = ActivityRecommendation::find($id);
        $payload = $request->only(['treatment', 'next_visit_recom']);
        $payload['updated_by'] = Auth::id();
        $qry->update($payload);
        return $this->sendResponse(new ActivityRecommendationResource($qry), 'Recommendation was successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ActivityRecommendation::find($id)->update(['isDeleted' => 'Y', 'deleted_by' => Auth::id()]);
        return $this->sendResponse([], 'Recommendations retrieved successfully.');
    }

    public function recommendations_by_activity(Request $request)
    {
        $recommendations = ActivityRecommendation::where($request->only(['patient_id', 'activity_id']))->where('isDeleted', 'N')->select('*')->paginate(10);
        return $this->sendResponse($recommendations, 'Recommendations retrieved successfully.');
    }

    public function recom_from_prev_activity(Request $request)
    {
        $recommendations = ActivityRecommendation::where(['patient_id' => $request->patient_id, 'isDeleted' => 'N', 'isHidden' => 'N'])
            ->where('activity_id', '<', $request->activity_id)->get();
        return $this->sendResponse($recommendations, 'Recommendations from previous activity retrieved successfully.');
    }

    public function hide_recommendation(Request $request)
    {
        $recom = ActivityRecommendation::find($request->id);
        $recom->update(['isHidden' => 'Y', 'hide_by' => Auth::id()]);
        return $this->sendResponse($recom, 'Recommendations was hide successfully.');
    }
}
