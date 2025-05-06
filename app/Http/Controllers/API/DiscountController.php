<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountResource;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Discount;
use App\Models\UserBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DiscountController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function branch_discounts(Request $request)
    {
        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();
        $isBranchExistOnUser = UserBranch::where(['user_id' => Auth::id(), 'branch_id' => $request->branchId])->count();
        $discounts = [];
        if ($isBranchExistOnUser) {
            $discounts = Discount::where('branch_id', $request->branchId)->get();
        }
        return $this->sendResponse(DiscountResource::collection($discounts), 'discounts retrieved successfully.');
    }

    public function branch_discounts_paginated(Request $request)
    {
        Validator::make(['branch_id' => $request->branchId], [
            'branch_id' => 'required|integer|exists:branches,id',
        ])->validate();
        // $isBranchExistOnUser = UserBranch::where(['user_id' => Auth::id(), 'branch_id' => $request->branchId])->count();
        $query = Discount::select('*');

        if (isset($request->branchId)) {
            $query->where('branch_id', $request->branchId);
        }

        if (isset($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $discounts = $query->paginate(10);
        return $this->sendResponse($discounts, 'discounts retrieved successfully.');
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
        $this->validate($request, Discount::rules());
        $payload = [
            'name' => $request->name,
            'is_fixed_amount' => $request->is_fixed_amount ? 'Y' : 'N',
            'discount_rate' => $request->discount_rate ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'branch_id' => $request->branch_id
        ];

        $isExist = Discount::where([
            'name' => $request->name,
            'branch_id' => $request->branch_id
        ])->count();

        if ($isExist) {
            return $this->sendError('Discount already exist.');
        }
        $expense_type = Discount::create($payload);

        return $this->sendResponse(new DiscountResource($expense_type), 'Discount created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense_type = Discount::find($id);

        if (is_null($expense_type)) {
            return $this->sendError('Discount not found.');
        }

        return $this->sendResponse(new DiscountResource($expense_type), 'Discount retrieved successfully.');
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
        $this->validate($request, Discount::rules());

        $payload = [
            'name' => $request->name,
            'is_fixed_amount' => $request->is_fixed_amount ? 'Y' : 'N',
            'discount_rate' => $request->discount_rate ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'branch_id' => $request->branch_id
        ];
        $isExist = Discount::where([
            'name' => $request->name,
            'branch_id' => $request->branch_id
        ])->where('id', '!=', $id)->count();

        if ($isExist) {
            return $this->sendError('Discount already exist.');
        }

        $res = Discount::find($id)->update($payload);
        if ($res) {
            $discount = Discount::find($id);
        }
        return $this->sendResponse(new DiscountResource($discount), 'Discount updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();

        return $this->sendResponse([], 'Discount deleted successfully.');
    }
}
