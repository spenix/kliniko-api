<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{PatientIntraOral};
use App\Http\Resources\PatientIntraOralResource;
use Illuminate\Support\Facades\{Auth, Validator};
use App\Http\Controllers\API\BaseController as BaseController;
use Exception;

class PatientIntraOralController extends BaseController
{
    protected $folder_name;
    public function __construct()
    {
        $this->folder_name = 'patient-intra-oral';
    }
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

        $patientIntraOral = PatientIntraOral::where('patient_id', $request->patient_id)
            ->orderBy('created_at', 'asc')
            ->selectRaw('*, IFNULL(date_taken, DATE_FORMAT(created_at, "%M %d, %Y")) as taken_date')
            ->get()
            ->map(function ($row) {
                $explodeArr = explode("/", $row->image_path);
                $arrPath = count($explodeArr) ? array_slice($explodeArr, -2, 2) : [];
                $path = '';
                foreach ($arrPath as $pathKey => $pathValue) {
                    $path .= ($pathKey ? '/' : '') . $pathValue;
                }
                $row['img64'] = img_enc_base64($path);
                return $row;
            });
        return $this->sendResponse(new PatientIntraOralResource($patientIntraOral), 'PatientIntraOral records retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, PatientIntraOral::rules());

            $payload = [
                'patient_id' => $request->patient_id,
                'row_rec' => $request->row_num,
                'column_rec' => $request->column_num,
                'created_by' => Auth::id()
            ];

            $image = convertBase64ToImage($request->imageFile, "patient-intra-oral-" . time() . "-" . sprintf('%08d', $request->patient_id) . "-" . date('Ymd') . "-" . $request->row_num . "-" . $request->column_num, $this->folder_name);
            $image_path = $image['path'];
            $payload['image_path'] = $image_path;

            $patientIntraOral = PatientIntraOral::create($payload);

            return $this->sendResponse(new PatientIntraOralResource($patientIntraOral), 'Patient Intra Oral record was saved successfully.');
        } catch (Exception $e) {
            return $e->getMessage();
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
        $patientIntraOral = PatientIntraOral::where('patient_id', $id)->first();

        if (is_null($patientIntraOral)) {
            return $this->sendError('Patient Intra Oral record not found.');
        }

        return $this->sendResponse(new PatientIntraOralResource($patientIntraOral), 'Patient Intra Oral record retrieved successfully.');
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
        $payload = $request->date_taken;
        PatientIntraOral::where('patient_id', $id)->update($request->only(['date_taken']));

        return $this->sendResponse($payload, 'Patient Intra Oral record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $patientIntraOral = PatientIntraOral::find($id);
            if ($patientIntraOral) {
                $arrData = explode("/", $patientIntraOral->image_path);
                if (file_exists(public_path($this->folder_name . '/' . end($arrData)))) {
                    @unlink(public_path($this->folder_name . '/' . end($arrData)));
                }
                PatientIntraOral::find($id)->delete();
            }

            return $this->sendResponse([], 'Patient Intra Oral record was deleted successfully.');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
