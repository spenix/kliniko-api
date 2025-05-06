<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\PaymentType;
use Validator;
use App\Http\Resources\PaymentTypeResource;
use Illuminate\Support\Facades\Auth;

class PaymentTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $patients = PaymentType::get();
        return $this->sendResponse(PaymentTypeResource::collection($patients), 'PaymentTypes retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, PaymentType::rules());
        $payload = [
            'name' => $request->name,
            'is_cash' => $request->is_cash ? 'Y' : 'N',
            'need_reference_details' => $request->need_reference_details ? 'Y' : 'N',
        ];
        $paymentType = PaymentType::create($payload);
        return $this->sendResponse(new PaymentTypeResource($paymentType), 'PaymentType created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paymentType = PaymentType::find($id);

        if (is_null($paymentType)) {
            return $this->sendError('PaymentType not found.');
        }

        return $this->sendResponse(new PaymentTypeResource($paymentType), 'PaymentType retrieved successfully.');
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

        $this->validate($request, PaymentType::rules());
        $payload = [
            'name' => $request->name,
            'is_cash' => $request->is_cash ? 'Y' : 'N',
            'need_reference_details' => $request->need_reference_details ? 'Y' : 'N',
        ];

        $res = PaymentType::find($id)->update($payload);

        $paymentType = [];
        if ($res) {
            $paymentType = PaymentType::find($id);
        }
        return $this->sendResponse(new PaymentTypeResource($paymentType), 'PaymentType updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentType $patient)
    {
        $patient->delete();

        return $this->sendResponse([], 'PaymentType deleted successfully.');
    }
}
