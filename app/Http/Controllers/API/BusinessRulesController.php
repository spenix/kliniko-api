<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{BusinessRule, User, UserBranch};
use App\Http\Resources\BusinessRuleResource;
use Illuminate\Support\Facades\{Auth, Validator};
use App\Http\Controllers\API\BaseController as BaseController;

class BusinessRulesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::selectRaw('id, name, role')->get()->groupBy('role');

        return $this->sendResponse(new BusinessRuleResource($users), 'System Users Users retrieved successfully.');
    }

    public function role_users(Request $request)
    {
        $br = UserBranch::join('users', 'users.id', 'user_branches.user_id')->where('user_branches.branch_id', '!=', $request->branchId)->whereIn('users.role', ['RC', 'DA'])->get()->map(function ($raw) {
            return $raw->user_id;
        });
        $users = User::whereNotIn('id', $br)->selectRaw('id, name, role')->get()->groupBy('role');
        return $this->sendResponse(new BusinessRuleResource($users), 'System Users Users retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (is_array($request->administrator)) {
            $payload = [];
            foreach ($request->administrator as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }

        if (is_array($request->operational_manager)) {
            $payload = [];
            foreach ($request->operational_manager as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }

        if (is_array($request->oic)) {
            $payload = [
                'user_id' => $request->oic['id'],
                'branch_id' => $request->branch_id,
                'created_by' => Auth::id(),
                'created_at' => now()
            ];
            UserBranch::create($payload);
        }

        if (is_array($request->receptionist)) {
            $payload = [];
            foreach ($request->receptionist as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }

        if (is_array($request->dental_assistant)) {
            $payload = [];
            foreach ($request->dental_assistant as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }
        $businessRule = UserBranch::where('branch_id', $request->branch_id)->get()->groupBy('role');

        return $this->sendResponse(new BusinessRuleResource($businessRule), 'System Users Record was created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $businessRule = UserBranch::join('users', 'users.id', 'user_branches.user_id')->where('user_branches.branch_id', $id)->selectRaw('users.name, users.id, users.role')->get()->groupBy('role');
        return $this->sendResponse(new BusinessRuleResource($businessRule), 'System Users retrieved successfully.');
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
        UserBranch::where(['branch_id' => $request->branch_id])->delete();

        if (is_array($request->administrator)) {
            $payload = [];
            foreach ($request->administrator as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_by' => Auth::id(),
                    'updated_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }

        if (is_array($request->operational_manager)) {
            $payload = [];
            foreach ($request->operational_manager as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_by' => Auth::id(),
                    'updated_at' => now()
                ];
            }

            UserBranch::insert($payload);
        }

        if (is_array($request->oic)) {
            $payload = [];
            foreach ($request->oic as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_by' => Auth::id(),
                    'updated_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }

        if (is_array($request->receptionist)) {
            $payload = [];
            foreach ($request->receptionist as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_by' => Auth::id(),
                    'updated_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }

        if (is_array($request->dental_assistant)) {
            $payload = [];
            foreach ($request->dental_assistant as $key => $value) {
                $payload[] = [
                    'user_id' => $value['id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_by' => Auth::id(),
                    'updated_at' => now()
                ];
            }
            UserBranch::insert($payload);
        }
        $businessRule = UserBranch::where('branch_id', $request->branch_id)->get()->groupBy('role');

        return $this->sendResponse(new BusinessRuleResource($businessRule), 'System Users Record was updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
