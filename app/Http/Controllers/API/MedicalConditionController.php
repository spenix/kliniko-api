<?php
     
namespace App\Http\Controllers\API;
     
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\MedicalCondition;
use Validator;
use App\Http\Resources\MedicalConditionResource;
     
class MedicalConditionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = MedicalCondition::all();
      
        return $this->sendResponse(MedicalConditionResource::collection($patients), 'MedicalConditions retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, MedicalCondition::rules());

        $data = $request->all();


        $patient = MedicalCondition::create($data);
     
        return $this->sendResponse(new MedicalConditionResource($patient), 'MedicalCondition created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patient = MedicalCondition::find($id);
    
        if (is_null($patient)) {
            return $this->sendError('MedicalCondition not found.');
        }
     
        return $this->sendResponse(new MedicalConditionResource($patient), 'MedicalCondition retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MedicalCondition $patient)
    {

        $this->validate($request, MedicalCondition::rules());
        $input = $request->all();
     
        $patient->first_name = $input['first_name'];
        $patient->middle_name = $input['middle_name'];
        $patient->last_name = $input['last_name'];
        $patient->address_line1 = $input['address_line1'];
        $patient->address_line2 = $input['address_line2'];
        $patient->address_line3 = $input['address_line3'];
        $patient->birth_date = $input['birth_date'];
        $patient->height = $input['height'];
        $patient->weight = $input['weight'];
        $patient->sex = $input['sex'];
        $patient->civil_status = $input['civil_status'];
        $patient->occupation = $input['occupation'];
        $patient->religion = $input['religion'];
        $patient->contact_no = $input['contact_no'];
        $patient->fb_account = $input['fb_account'];
        $patient->nationality = $input['nationality'];
        $patient->general_physician = $input['general_physician'];
        $patient->medical_last_visit = $input['medical_last_visit'];
        $patient->has_serious_illness = $input['has_serious_illness'];
        $patient->describe_illness = $input['describe_illness'];
        $patient->save();
     
        return $this->sendResponse(new MedicalConditionResource($patient), 'MedicalCondition updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicalCondition $patient)
    {
        $patient->delete();
     
        return $this->sendResponse([], 'MedicalCondition deleted successfully.');
    }
}