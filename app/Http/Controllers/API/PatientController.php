<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\PatientResource;
use App\Helper\Helper;
use App\Http\Resources\BalanceHistoryResource;
use App\Models\{Patient, KeyGen, User, PatientMedicalCondition, UserBranch};
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\{Validator, Hash, Auth, DB};

class PatientController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();
        $isBranchExistOnUser = UserBranch::where(['user_id' => Auth::id(), 'branch_id' => $request->branchId])->count();
        $patients = [];
        if ($isBranchExistOnUser) {
            $patients = Patient::where('branch_id', $request->branchId)->get();
        }
        return $this->sendResponse(PatientResource::collection($patients), 'Patients retrieved successfully.');
    }

    public function patient_list(Request $request)
    {
        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();

        $query = Patient::selectRaw('*, floor(DATEDIFF(now(), birth_date)/365.2425) age')->where('branch_id', $request->branchId);

        if (intval($request->search) && is_int(intval($request->search))) {
            $query->whereRaw('floor(DATEDIFF(now(), birth_date)/365.2425) = ' . intval($request->search));
        }

        if ($request->search) {
            $query->where(function ($queryV2) use ($request) {
                $queryV2->where('patient_no', 'like', '%' . $request->search . '%');
                $queryV2->orWhere('first_name', 'like', '%' . $request->search . '%');
                $queryV2->orWhere('middle_name', 'like', '%' . $request->search . '%');
                $queryV2->orWhere('last_name', 'like', '%' . $request->search . '%');
                $queryV2->orWhere('patient_no', 'like', '%' . $request->search . '%');
                $queryV2->orWhere('occupation', 'like', '%' . $request->search . '%');
                $queryV2->orWhere('sex', 'like', '%' . $request->search . '%');
                $queryV2->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'like', '%' . $request->search . '%');
            });
        }
        $patients = $query->paginate(10);

        return $this->sendResponse($patients, 'Patients retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paginated_patient_list(Request $request)
    {
        $payload = [
            'search_key' => $request->search_key,
            'branch_id' => $request->branchId,
        ];

        Validator::make($payload, [
            'search_key' => 'nullable|string|max:255',
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();

        $query = Patient::selectRaw('*, DATE_FORMAT(birth_date, "%M %d, %Y") as bday');

        $query->where('branch_id', $request->branchId);

        if ($payload['search_key']) {
            $query->where(function ($queryV2) use ($payload) {
                $queryV2->where('patient_no', 'like', '%' . $payload['search_key'] . '%');
                $queryV2->orWhere('first_name', 'like', '%' . $payload['search_key'] . '%');
                $queryV2->orWhere('middle_name', 'like', '%' . $payload['search_key'] . '%');
                $queryV2->orWhere('last_name', 'like', '%' . $payload['search_key'] . '%');
                $queryV2->orWhere(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'like', '%' . $payload['search_key'] . '%');
            });
        }
        $patients = $query->paginate(10);

        return $this->sendResponse($patients, 'List of patients.');
    }

    public function store(Request $request)
    {
        $this->validate($request, Patient::rules());
        $patient_no_prefix = $request->patient_no_prefix;
        $branch_id = $request->branch_id;

        $year_month = date('ym');
        $key_gen = KeyGen::create([
            "year_month" => $year_month,
            "branch_id" => $branch_id,
            "prefix" => $patient_no_prefix
        ]);

        $patient_data = [
            'branch_id' => $branch_id,
            'patient_no' => $patient_no_prefix . "-" . $year_month . "-" . str_pad($key_gen->id, 4, '0', STR_PAD_LEFT),
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'birth_date' => $request->birth_date,
            'height' => $request->height,
            'weight' => $request->weight,
            'sex' => $request->sex,
            'civil_status' => $request->civil_status,
            'occupation' => $request->occupation,
            'religion' => $request->religion,
            'contact_no' => $request->contact_no,
            'email_address' => $request->email_address,
            'fb_account' => $request->user_social['facebook_url'],
            'twitter_account' => $request->user_social['twitter_url'],
            'instagram_account' => $request->user_social['instagram_url'],
            'linkedin_account' => $request->user_social['linkedin_url'],
            'nationality' => $request->nationality,
            'general_physician' => $request->general_physician,
            'medical_last_visit' => $request->medical_last_visit,
            'has_serious_illness' => $request->has_serious_illness,
            'describe_illness' => $request->describe_illness,
            'has_boold_transfusion' => $request->has_boold_transfusion ? 'Y' : 'N',
            'approximate_date' => $request->approximate_date,
            'is_pregnant' => $request->is_pregnant ? 'Y' : 'N',
            'taking_pills' => $request->taking_pills ? 'Y' : 'N',
            'taking_any_medications' => $request->taking_any_medications ? 'Y' : 'N',
            'if_has_med_specify' => $request->if_has_med_specify,
        ];

        $patient = Patient::create($patient_data);
        if ($request->isUploadImg && isset($patient->id)) {
            $image = convertBase64ToImage($request->profile_image, "patient-" . time() . "-" . sprintf('%08d',  $patient->id) . "-" . $branch_id, 'patient-profiles');
            $image_path = $image['path'];
            $patient->update(['avatar' => $image_path]);
        }

        $patient->medical_conditions()->attach($request->medical_condition);

        return $this->sendResponse(new PatientResource($patient), 'Patient created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $isExistInBranch = Patient::where(['id' => $id, 'branch_id' => $request->branchId])->count();

        if ($isExistInBranch) {
            $patient = Patient::with('medical_conditions')->where('id', $id)->selectRaw('*, DATE_FORMAT(birth_date, "%M %d, %Y") as bday')->first();
            if (is_null($patient)) {
                return $this->sendError('Patient not found.');
            }
            return $this->sendResponse(new PatientResource($patient), 'Patient retrieved successfully.');
        } else {
            return $this->sendError('404 Error', 'Patient not found.', 404);
        }
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
        $this->validate($request, Patient::rules());

        $patient_data = [
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'birth_date' => $request->birth_date,
            'height' => $request->height,
            'weight' => $request->weight,
            'sex' => $request->sex,
            'civil_status' => $request->civil_status,
            'occupation' => $request->occupation,
            'religion' => $request->religion,
            'contact_no' => $request->contact_no,
            'email_address' => $request->email_address,
            'fb_account' => $request->user_social['facebook_url'],
            'twitter_account' => $request->user_social['twitter_url'],
            'instagram_account' => $request->user_social['instagram_url'],
            'linkedin_account' => $request->user_social['linkedin_url'],
            'nationality' => $request->nationality,
            'general_physician' => $request->general_physician,
            'medical_last_visit' => $request->medical_last_visit,
            'has_serious_illness' => $request->has_serious_illness,
            'describe_illness' => $request->describe_illness,
            'has_boold_transfusion' => $request->has_boold_transfusion ? 'Y' : 'N',
            'approximate_date' => $request->approximate_date,
            'is_pregnant' => $request->is_pregnant ? 'Y' : 'N',
            'taking_pills' => $request->taking_pills ? 'Y' : 'N',
            'taking_any_medications' => $request->taking_any_medications ? 'Y' : 'N',
            'if_has_med_specify' => $request->if_has_med_specify,
        ];

        if ($request->isUploadImg) {
            $image = convertBase64ToImage($request->profile_image, "patient-" . time() . "-" . sprintf('%08d',  $id) . "-" . $request->branch_id, 'patient-profiles');
            $image_path = $image['path'];
            $patient_data['avatar'] = $image_path;
        }

        $patient = Patient::find($id);
        if ($patient->avatar && $request->isUploadImg) {
            $imgExplodeUrl = explode("/", $patient->avatar);
            if (count($imgExplodeUrl)) {
                $imgWithType = explode(".", end($imgExplodeUrl));
                removeFileExist($imgWithType[0], 'patient-profiles');
            }
        }

        $patient->update($patient_data);
        if ($patient) {
            PatientMedicalCondition::where('patient_id', $id)
                ->whereNotIn('medical_condition_id', $request->medical_condition)
                ->delete();
            foreach ($request->medical_condition as $key => $value) {
                $countPMC = PatientMedicalCondition::where(['patient_id' => $id, 'medical_condition_id' => $value])->count();
                if (!$countPMC) {
                    // dump(['patient_id' => $id, 'medical_condition_id' => $value]);
                    PatientMedicalCondition::create(['patient_id' => $id, 'medical_condition_id' => $value]);
                }
            }
        }

        $patient = Patient::with('medical_conditions')->where('id', $id)->first();
        return $this->sendResponse(new PatientResource($patient), 'Patient edited successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return $this->sendResponse([], 'Patient deleted successfully.');
    }

    public function patient_balance_history(Patient $patient)
    {
        $history = $patient->balance_histories;
        return $this->sendResponse(BalanceHistoryResource::collection($history), 'Balance History retrieved successfully.');
    }

    public function patient_with_balance_list(Request $request)
    {
        $patient = Patient::selectRaw('patient_no, CONCAT(first_name, " ", IFNULL(middle_name, ""), IFNULL(middle_name, ""), last_name) as name, CONCAT("₱",FORMAT(balance,2,"en_US")) as balance');
        $patient->where('balance', '>', 0);
        if (isset($request->branchId)) {
            $patient->where('branch_id', $request->branchId);
        }
        if (isset($request->search)) {
            $patient->where(function ($query) use ($request) {
                $query->orWhereRaw('CONCAT(first_name, " ", IFNULL(middle_name, ""), IFNULL(middle_name, ""), last_name) like ? ', '%' . $request->search . '%');
                $query->orWhereRaw('first_name like ? ', '%' . $request->search . '%');
                $query->orWhereRaw('middle_name like ? ', '%' . $request->search . '%');
                $query->orWhereRaw('last_name like ? ', '%' . $request->search . '%');
                $query->orWhereRaw('CONCAT("₱ ",FORMAT(balance,2,"en_US")) like ? ', '%' . $request->search . '%');
            });
        }
        $patients = $patient->paginate(10);
        return $this->sendResponse($patients, 'Patients with balance retrieved successfully.');
    }

    public function patients_grand_total_balance(Request $request)
    {
        $patient = Patient::select('balance');
        $patient->where('balance', '>', 0);
        if (isset($request->branchId)) {
            $patient->where('branch_id', $request->branchId);
        }
        $amtBalance = $patient->get()->sum('balance');
        return response()->json(['amt_balance' => number_format($amtBalance, 2)]);
    }
}
