<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'branch_id' => 'exists:accounts,id',
            'email' => 'required|email',
            'role' => 'required|in:SA,AD,OM,OIC,RC,DA',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ])->validate();

        $input = [ // app\Http\Controllers\API\RegisterController.php:32
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "role" => $request->role,
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "middlename" => $request->middlename
        ];
        $user = User::create($input);
        $success['token'] =  $user->createToken('KliniKo')->accessToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('KliniKo')->accessToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout()
    {
        try {
            $token = Auth::user()->token();
            $token->revoke();

            return $this->sendResponse([], 'User logout successfully.');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
