<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Branch;
use Validator;
use App\Http\Resources\BranchResource;
use App\Models\Clinic;

class BranchController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branch::all();
        return $this->sendResponse(BranchResource::collection($branches), 'Branch retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Branch::rules());

        $payload = [
            'clinic_id' => $request->clinic_id,
            'name' => $request->name,
            'patient_no_prefix' => $request->patient_no_prefix,
            'address' => $request->address,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'fb_page' => $request->fb_page,
        ];

        $branch = Branch::create($payload);

        if ($request->isUploadImg && isset($branch->id)) {
            $image = convertBase64ToImage($request->logo, "branch-logo-" . time() . "-" . sprintf('%08d', $branch->id) . "-" . date('Ymd'), 'branch-logos');
            $image_path = url('/') . $image['path'];
            $branch->update(['logo' => $image_path]);
        }

        return $this->sendResponse(new BranchResource($branch), 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $branch = Branch::find($id);

        if (is_null($branch)) {
            return $this->sendError('Branch not found.');
        }

        return $this->sendResponse(new BranchResource($branch), 'Branch retrieved successfully.');
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

        $this->validate($request, Branch::rules());
        $payload = [
            'clinic_id' => $request->clinic_id,
            'name' => $request->name,
            'patient_no_prefix' => $request->patient_no_prefix,
            'address' => $request->address,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            'fb_page' => $request->fb_page,
        ];

        if ($request->isUploadImg) {
            $image = convertBase64ToImage($request->logo, "branch-logo-" . time() . "-" . sprintf('%08d', $id) . "-" . date('Ymd'), 'branch-logos');
            $image_path = url('/') . $image['path'];
            $payload['logo'] = $image_path;
        }
        $branch = Branch::find($id);
        if ($branch->logo && $request->isUploadImg) {
            $imgExplodeUrl = explode("/", $branch->logo);
            if (count($imgExplodeUrl)) {
                $imgWithType = explode(".", end($imgExplodeUrl));
                removeFileExist($imgWithType[0], 'branch-logos');
            }
        }
        $branch->update($payload);
        return $this->sendResponse(new BranchResource($branch), 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return $this->sendResponse([], 'Branch deleted successfully.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function branches_per_clinic($id)
    {
        $clinic = Clinic::find($id);
        $branches = Branch::where('clinic_id', $id)->get();

        $response = array(
            'clinic' => $clinic,
            'branches' => BranchResource::collection($branches)
        );

        return $this->sendResponse($response, 'Branch retrieved successfully.');
    }
}
