<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Hash, Auth, DB};
use App\Http\Controllers\API\BaseController as BaseController;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = User::all();

        return $this->sendResponse(UserResource::collection($user), 'Users retrieved successfully.');
    }

    public function roles_assigned_name($role = '')
    {
        switch ($role) {
            case 'SA':
                return 'SYSTEM ADMINISTRATOR';
                break;
            case 'AD':
                return 'ADMINISTRATOR';
                break;
            case 'OM':
                return 'OPERATIONAL MANAGER';
                break;
            case 'OIC':
                return 'OFFICER IN CHARGE';
                break;
            case 'DA':
                return 'DENTAL ASSISTANT';
                break;
            default:
                return 'Receptionist';
                break;
        }
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
        $this->validate($request, User::rules());

        $payload = [
            'name' => $request->firstname . ' ' . $request->middlename . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'middlename' => $request->middlename,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ];

        $user = User::create($payload);
        if ($request->isUploadImg && isset($user->id)) {
            $image = convertBase64ToImage($request->profile_path, "user-profile-" . time() . "-" . sprintf('%08d', $user->id) . "-" . date('Ymd'), 'user-profiles');
            $image_path = $image['path'];
            $user->update(['profile_path' => $image_path]);
        }
        return $this->sendResponse(new UserResource($user), 'User was created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
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

        Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|in:SA,AD,OM,OIC,RC,DA',
        ])->validate();

        $payload = [
            'name' => $request->firstname . ' ' . $request->middlename . ' ' . $request->lastname,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'middlename' => $request->middlename,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->password != '**********') {
            $payload['password'] = bcrypt($request->password);
        }

        if ($request->isUploadImg) {
            $image = convertBase64ToImage($request->profile_path, "user-profile-" . time() . "-" . sprintf('%08d', $id) . "-" . date('Ymd'), 'user-profiles');
            $image_path = $image['path'];
            $payload['profile_path'] = $image_path;
        }

        $isExist = User::where('email', $request->email)->where('id', '!=', $id)->count();
        if ($isExist) {
            return $this->sendError('Oops, Email has been used already.');
        }
        $user = User::find($id);
        if ($user->profile_path && $request->isUploadImg) {
            $imgExplodeUrl = explode("/", $user->profile_path);
            if (count($imgExplodeUrl)) {
                $imgWithType = explode(".", end($imgExplodeUrl));
                removeFileExist($imgWithType[0], 'user-profiles');
            }
        }
        $user->update($payload);
        return $this->sendResponse($user ?: [], 'User was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return $this->sendResponse([], 'User deleted successfully.');
    }

    public function user_by_role($role)
    {

        $users = User::where("role", $role)->get();

        return $this->sendResponse(UserResource::collection($users), 'Branch retrieved successfully.');
    }

    public function user_details()
    {
        $user = User::with('branches')->where('id', Auth::id())->get()->map(function ($row) {
            foreach ($row->branches as $key => $value) {
                $explodeArr = explode("/", $value->logo);
                $arrPath = count($explodeArr) ? array_slice($explodeArr, -2, 2) : [];

                $path = '';
                foreach ($arrPath as $pathKey => $pathValue) {
                    $path .= ($pathKey ? '/' : '') . $pathValue;
                }
                $row->branches[$key]['logoV2'] = img_enc_base64($path);
            }
            return $row;
        })->first();

        return $this->sendResponse(new UserResource($user), 'User details retrieved successfully.');
    }

    public function users_paginations(Request $request)
    {
        $query = User::select('*', DB::raw('(CASE
        WHEN role = "SA" THEN "SYSTEM ADMINISTRATOR"
        WHEN role = "AD" THEN "ADMINISTRATOR"
        WHEN role = "OM" THEN "OPERATIONAL MANAGER"
        WHEN role = "OIC" THEN "OFFICER IN CHARGE"
        WHEN role = "DA" THEN "DENTAL ASSISTANT"
        ELSE "Receptionist"
        END) AS role_name'));

        if (isset($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
            $query->orWhere('email', 'like', '%' . $request->search . '%');
            $query->orWhereRaw('(CASE
                WHEN role = "SA" THEN "SYSTEM ADMINISTRATOR"
                WHEN role = "AD" THEN "ADMINISTRATOR"
                WHEN role = "OM" THEN "OPERATIONAL MANAGER"
                WHEN role = "OIC" THEN "OFFICER IN CHARGE"
                WHEN role = "DA" THEN "DENTAL ASSISTANT"
                ELSE "Receptionist"
            END) like ?', '%' . $request->search . '%');
        }
        $users = $query->paginate(10);
        return $this->sendResponse($users, 'Users retrieved successfully.');
    }
}
