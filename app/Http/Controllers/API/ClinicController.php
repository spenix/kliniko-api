<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Clinic;
use Validator;
use App\Http\Resources\ClinicResource;
     
class ClinicController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clinics = Clinic::withCount('branches')->get();
      
        return $this->sendResponse(ClinicResource::collection($clinics), 'Clinic retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, Clinic::rules());

        $data = $request->all();

        $clinic = Clinic::create($data);
     
        return $this->sendResponse(new ClinicResource($clinic), 'Clinic created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $clinic = Clinic::find($id);
    
        if (is_null($clinic)) {
            return $this->sendError('Clinic not found.');
        }
     
        return $this->sendResponse(new ClinicResource($clinic), 'Clinic retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clinic $clinic)
    {

        $this->validate($request, Clinic::rules());
        $input = $request->all();
     
        $clinic->name = $input['name'];
        $clinic->save();
     
        return $this->sendResponse(new ClinicResource($clinic), 'Clinic updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clinic $clinic)
    {
        $clinic->delete();
     
        return $this->sendResponse([], 'Clinic deleted successfully.');
    }
}