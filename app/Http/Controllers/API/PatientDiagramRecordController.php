<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{PatientDiagramRecord, PatientDiagramDataRecord};
use App\Http\Resources\PatientDiagramRecordResource;
use Illuminate\Support\Facades\{Auth, Validator};
use App\Http\Controllers\API\BaseController as BaseController;

class PatientDiagramRecordController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Validator::make(['patient_id' => $request->patient_id], [
            'patient_id' => 'required|integer|exists:patients,id',
        ])->validate();
        $patientDiagramRecords = PatientDiagramRecord::with('data_records')->leftJoin('users', 'users.id', 'patient_diagram_records.created_by')
            ->where('patient_id', $request->patient_id)
            ->selectRaw('patient_diagram_records.id, patient_diagram_records.patient_id, DATE_FORMAT(patient_diagram_records.created_at, "%M %d, %Y") as createdAt, patient_diagram_records.remarks, users.name as createdBy')
            ->orderBy('id', 'desc')
            ->get();
        return $this->sendResponse(new PatientDiagramRecordResource($patientDiagramRecords), 'Patient diagram records retrieved successfully.');
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
        // dd($request->all());
        $this->validate($request, PatientDiagramRecord::rules());

        $payload = [
            'patient_id' => $request->patient_id,
            'remarks' => $request->remarks,
            'created_by' => Auth::id()
        ];
        $patientDiagramRecord = PatientDiagramRecord::create($payload);
        if ($patientDiagramRecord) {
            $payload2 = [];
            foreach (['right', 'left'] as $key => $value) {
                foreach ([1, 2, 3, 4] as $param_value) {
                    foreach ($request[$value . $param_value] as $recVal) {
                        $payload2[] = [
                            'diagram_record_id' => $patientDiagramRecord->id,
                            'teeth_group' => $value . $param_value,
                            'code' => $recVal['code'],
                            'code_text' => $recVal['code_text'] ? $recVal['code_text']['value'] : null,
                            'color_code' => $recVal['color_code'],
                            'check_flag' => $recVal['value'] ? 'Y' : 'N',
                            'created_at' => now()
                        ];
                    }
                }
            }
            PatientDiagramDataRecord::insert($payload2);
        }

        $patientDiagramRecord = PatientDiagramRecord::with('data_records')->where('id', $patientDiagramRecord->id)->get()->first();

        return $this->sendResponse(new PatientDiagramRecordResource($patientDiagramRecord), 'Patient Diagram Rocord created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patientDiagramRecord = PatientDiagramRecord::with('data_records')
            ->leftJoin('users', 'users.id', 'patient_diagram_records.created_by')
            ->where('patient_id', $id)
            ->selectRaw('patient_diagram_records.id, patient_diagram_records.patient_id, DATE_FORMAT(patient_diagram_records.created_at, "%M %d, %Y") as createdAt, patient_diagram_records.remarks, users.name as createdBy')
            ->orderBy('id', 'desc')
            ->get()->first();
        if (is_null($patientDiagramRecord)) {
            return $this->sendError('Patient Diagram Record not found.');
        }
        return $this->sendResponse(new PatientDiagramRecordResource($patientDiagramRecord), 'Patient Diagram Record retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $this->validate($request, PatientDiagramRecord::rules());

        $payload = [
            'patient_id' => $request->patient_id,
            'remarks' => $request->remarks,
            'created_by' => Auth::id()
        ];
        $patientDiagramRecord = PatientDiagramRecord::find($id)->update($payload);
        if ($patientDiagramRecord) {
            foreach (['right', 'left'] as $key => $value) {
                foreach ([1, 2, 3, 4] as $param_value) {
                    foreach ($request[$value . $param_value] as $recVal) {
                        $payload2 = [
                            'diagram_record_id' => $id,
                            'teeth_group' => $value . $param_value,
                            'code' => $recVal['code'],
                            'code_text' => $recVal['code_text'],
                            'color_code' => $recVal['color_code'],
                            'check_flag' => $recVal['value'] ? 'Y' : 'N',
                        ];
                        if ($recVal['id']) {
                            PatientDiagramDataRecord::find($recVal['id'])->update($payload2);
                        } else {
                            PatientDiagramDataRecord::create($payload2);
                        }
                    }
                }
            }
        }
        $patientDiagramRecord = PatientDiagramRecord::with('data_records')->where('id', $id)->get()->first();
        return $this->sendResponse(new PatientDiagramRecordResource($patientDiagramRecord), 'Patient Diagram Rocord updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        PatientDiagramRecord::find($id)->delete();
        return $this->sendResponse([], 'Patient Diagram Record was deleted successfully.');
    }
}
