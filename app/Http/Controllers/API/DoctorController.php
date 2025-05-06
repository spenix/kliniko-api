<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Doctor;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class DoctorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $doctors = Doctor::all();

        return $this->sendResponse(DoctorResource::collection($doctors), 'Doctors retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Doctor::rules());

        $payload = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'email_address' => $request->email_address,
            'birth_date' => $request->birth_date,
            'height' => $request->height,
            'weight' => $request->weight,
            'sex' => $request->sex,
            'civil_status' => $request->civil_status,
            'job_title' => $request->job_title,
            'license_no' => $request->license_no,
            'contact_no' => $request->contact_no,
            'fb_account' => $request->fb_account,
            'twitter_account' => $request->twitter_account,
            'instagram_account' => $request->instagram_account,
            'linkedin_account' => $request->linkedin_account,
            'nationality' => $request->nationality,
        ];

        if ($request->branch_id) {
            $payload['branch_id'] = $request->branch_id;
        }

        $doctor = Doctor::create($payload);

        if ($request->isUploadImg) {
            $image = convertBase64ToImage($request->profile_image, "doctor-" . time() . "-" . sprintf('%08d', $doctor->id) . "-" . date('Ymd'), 'doctor-profiles');
            $image_path = $image['path'];
            $doctor->update(['avatar' => $image_path]);
        }

        return $this->sendResponse(new DoctorResource($doctor), 'Doctor created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $doctor = Doctor::find($id);

        if (is_null($doctor)) {
            return $this->sendError('Doctor not found.');
        }

        return $this->sendResponse(new DoctorResource($doctor), 'Doctor retrieved successfully.');
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
        $this->validate($request, Doctor::rules());
        $payload = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'email_address' => $request->email_address,
            'birth_date' => $request->birth_date,
            'height' => $request->height,
            'weight' => $request->weight,
            'sex' => $request->sex,
            'civil_status' => $request->civil_status,
            'job_title' => $request->job_title,
            'license_no' => $request->license_no,
            'contact_no' => $request->contact_no,
            'fb_account' => $request->fb_account,
            'twitter_account' => $request->twitter_account,
            'instagram_account' => $request->instagram_account,
            'linkedin_account' => $request->linkedin_account,
            'nationality' => $request->nationality,
        ];

        if ($request->isUploadImg) {
            $image = convertBase64ToImage($request->profile_image, "doctor-" . time() . "-" . sprintf('%08d', $id) . "-" . date('Ymd'), 'doctor-profiles');
            $image_path = $image['path'];
            $payload['avatar'] = $image_path;
        }

        if ($request->branch_id) {
            $payload['branch_id'] = $request->branch_id;
        }

        $doctor = Doctor::find($id);
        if ($doctor->avatar && $request->isUploadImg) {
            $imgExplodeUrl = explode("/", $doctor->avatar);
            if (count($imgExplodeUrl)) {
                $imgWithType = explode(".", end($imgExplodeUrl));
                removeFileExist($imgWithType[0], 'doctor-profiles');
            }
        }
        $doctor->update($payload);

        return $this->sendResponse($doctor ? new DoctorResource(Doctor::find($id)) : [], 'Doctor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return $this->sendResponse([], 'Doctor deleted successfully.');
    }

    public function doctors_list(Request $request)
    {
        FacadesValidator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();

        $query = Doctor::where(['branch_id' => $request->branchId]);

        if (isset($request->search)) {
            $query->where('first_name', 'like', '%' . $request->search . '%');
            $query->orWhere('last_name', 'like', '%' . $request->search . '%');
            $query->orWhere('job_title', 'like', '%' . $request->search . '%');
            $query->orWhere('sex', 'like', '%' . $request->search . '%');
        }

        $doctors = $query->paginate(10);

        return $this->sendResponse($doctors, 'Doctors retrieved successfully.');
    }

    public function branch_doctors(Request $request)
    {

        $dtrs = Doctor::where('branch_id', $request->branchId);
        if (isset($request->qry)) {
            $dtrs->where(function ($query) use ($request) {
                $query->orWhereRaw('first_name like ? ', '%' . $request->qry . '%');
                $query->orWhereRaw('last_name like ? ', '%' . $request->qry . '%');
                $query->orWhereRaw('CONCAT(first_name, " ", last_name) like ? ', '%' . $request->qry . '%');
            });
        }

        $doctors = $dtrs->get();
        return $this->sendResponse(DoctorResource::collection($doctors), 'Doctors retrieved successfully.');
    }
}
