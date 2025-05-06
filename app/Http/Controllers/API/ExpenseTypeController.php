<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ExpenseType;
use Validator;
use App\Http\Resources\ExpenseTypeResource;
use App\Models\UserBranch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class ExpenseTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $expense_types = ExpenseType::all();

        return $this->sendResponse(ExpenseTypeResource::collection($expense_types), 'ExpenseTypes retrieved successfully.');
    }

    public function branch_expense_type(Request $request)
    {
        FacadesValidator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();
        $isBranchExistOnUser = UserBranch::where(['user_id' => Auth::id(), 'branch_id' => $request->branchId])->count();
        $discounts = [];
        if ($isBranchExistOnUser) {
            $discounts = ExpenseType::where('branch_id', $request->branchId)->get();
        }
        return $this->sendResponse(ExpenseTypeResource::collection($discounts), 'discounts retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, ExpenseType::rules());
        $payload = [
            'name' => $request->name,
            'description' => $request->description ?? '',
            'clinic_id' => $request->clinic_id,
            'branch_id' => $request->branch_id
        ];

        $isExist = ExpenseType::where([
            'name' => $request->name,
            'clinic_id' => $request->clinic_id,
            'branch_id' => $request->branch_id
        ])->count();

        if ($isExist) {
            return $this->sendError('Expense Type already exist.');
        }

        $expense_type = ExpenseType::create($payload);

        return $this->sendResponse(new ExpenseTypeResource($expense_type), 'ExpenseType created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense_type = ExpenseType::find($id);

        if (is_null($expense_type)) {
            return $this->sendError('ExpenseType not found.');
        }

        return $this->sendResponse(new ExpenseTypeResource($expense_type), 'ExpenseType retrieved successfully.');
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

        $this->validate($request, ExpenseType::rules());

        $payload = [
            'name' => $request->name,
            'description' => $request->description ?? '',
            'clinic_id' => $request->clinic_id,
            'branch_id' => $request->branch_id
        ];
        $isExist = ExpenseType::where([
            'name' => $request->name,
            'clinic_id' => $request->clinic_id,
            'branch_id' => $request->branch_id
        ])->where('id', '!=', $id)->count();

        if ($isExist) {
            return $this->sendError('Expense Type already exist.');
        }

        $res = ExpenseType::find($id)->update($payload);
        if ($res) {
            $expense_type = ExpenseType::find($id);
        }
        return $this->sendResponse(new ExpenseTypeResource($expense_type), 'ExpenseType updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseType $expense_type)
    {
        $expense_type->delete();

        return $this->sendResponse([], 'ExpenseType deleted successfully.');
    }
}
