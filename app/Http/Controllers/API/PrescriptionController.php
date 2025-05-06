<?php

namespace App\Http\Controllers\API;

use App\Events\PrescriptionEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\PrescriptionResource;
use App\Models\{Prescription, PrescriptionItem};
use Illuminate\Support\Facades\Event;

class PrescriptionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Validator::make($request->only(['patient_id']), [
            'patient_id' => 'required|integer|exists:patients,id',
        ])->validate();

        $query = Prescription::with('prescription_items')->join('doctors', 'doctors.id', 'prescriptions.doctor_id')->selectRaw('
        prescriptions.id, 
        LPAD(prescriptions.id, 8, "0") as display_id,
        DATE_FORMAT(prescriptions.created_at, "%M %d, %Y") as created_dt, 
        prescriptions.description, 
        prescriptions.doctor_id, 
        prescriptions.patient_id, 
        CONCAT(doctors.first_name, " ", doctors.middle_name, " ",  doctors.last_name) as doctor,
        doctors.license_no
        ')->where('patient_id', $request->patient_id);

        if (isset($request->search)) {
            $query->where(function ($query2) use ($request) {
                $query2->whereRaw('LPAD(prescriptions.id, 8, "0") like ? ', '%' . $request->search . '%');
                $query2->orWhereRaw('DATE_FORMAT(prescriptions.created_at, "%M %d, %Y") like ? ', '%' . $request->search . '%');
                $query2->orWhereRaw('description like ? ', '%' . $request->search . '%');
            });
        }
        $prescriptions = $query->paginate(10);

        return $this->sendResponse(new PrescriptionResource($prescriptions), 'Prescription retrieved successfully.');
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
        $this->validate($request, Prescription::rules());
        $payload = [
            'patient_id' => $request->patient_id,
            'description' => $request->description,
            'doctor_id' => $request->doctor_id
        ];
        if (isset($request->activity_id)) {
            $payload['activity_id'] = $request->activity_id;
        }

        $prescription = Prescription::create($payload);

        if ($prescription) {
            $items = [];
            foreach ($request->prescriptionList as $key => $value) {
                $itemPayload = [
                    'prescription_id' => $prescription->id,
                    'quantity' => $value['qty'],
                    'name' => $value['name'],
                    'uni_of_measurement' => $value['unit'],
                    'description' => $value['desc'],
                    'created_at' => now()
                ];
                Validator::make($itemPayload, [
                    'prescription_id' => 'required|integer|exists:prescriptions,id',
                    'quantity' => 'required|numeric',
                    'name' => 'required|string|max:255',
                    'uni_of_measurement' => 'required|string|max:255',
                    'description' => 'nullable|string',
                ])->validate();
                $items[] = $itemPayload;
            }
            $prescriptionItem =  PrescriptionItem::insert($items);
        }

        $resPrescription = Prescription::with('prescription_items')->where('id', $prescription->id)->get();

        if (isset($request->activity_id)) {
            $channel = "activity-prescription-" . $request->activity_id;
            Event::dispatch(new PrescriptionEvent(new PrescriptionResource($resPrescription), $channel));
        }
        return $this->sendResponse($resPrescription, 'Prescription was successfully saved.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prescription = Prescription::with('prescription_items')->where('id', $id)->get();
        if (is_null($prescription)) {
            return $this->sendError('Prescription not found.');
        }
        return $this->sendResponse(new PrescriptionResource($prescription), 'Prescription retrieved successfully.');
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
        $this->validate($request, Prescription::rules());
        $prescription = Prescription::find($id)->update([
            'patient_id' => $request->patient_id,
            'description' => $request->description,
            'doctor_id' => $request->doctor_id
        ]);
        if ($prescription) {
            PrescriptionItem::where('prescription_id', $id)->delete();
            $items = [];
            foreach ($request->prescriptionList as $key => $value) {
                $itemPayload = [
                    'prescription_id' => $id,
                    'quantity' => $value['qty'],
                    'name' => $value['name'],
                    'uni_of_measurement' => $value['unit'],
                    'description' => $value['desc'],
                    'created_at' => now()
                ];
                Validator::make($itemPayload, [
                    'prescription_id' => 'required|integer|exists:prescriptions,id',
                    'quantity' => 'required|numeric',
                    'name' => 'required|string|max:255',
                    'uni_of_measurement' => 'required|string|max:255',
                    'description' => 'nullable|string',
                ])->validate();
                $items[] = $itemPayload;
            }
            $prescriptionItem =  PrescriptionItem::insert($items);
        }

        $resPrescription = Prescription::with('prescription_items')->where('id', $id)->get();

        return $this->sendResponse($resPrescription, 'Prescription was successfully edited.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return $this->sendResponse([], 'Prescription deleted successfully.');
    }

    public function prescriptions_by_paginate(Request $request)
    {
        $prescriptions = Prescription::with('prescription_items')
            ->join('doctors', 'doctors.id', 'prescriptions.doctor_id')
            ->selectRaw(
                '
            prescriptions.id,
            LPAD(prescriptions.id, 8, "0") as display_id,
            DATE_FORMAT(prescriptions.created_at, "%M %d, %Y") as created_dt, 
            prescriptions.description, prescriptions.doctor_id, 
            prescriptions.patient_id, 
            CONCAT(doctors.first_name, " ", doctors.middle_name, " ",  doctors.last_name) as doctor'
            )
            ->where($request->only(['patient_id', 'activity_id']))
            ->paginate(10);

        return $this->sendResponse(new PrescriptionResource($prescriptions), 'Prescription retrieved successfully.');
    }
}
