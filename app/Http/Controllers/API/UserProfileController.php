<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\{Validator, Hash, Auth};
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\UserProfileResource;

class UserProfileController extends BaseController
{

    public function change_password(Request $request)
    {
        request()->validate([
            'current_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8'
        ]);
        try {
            if (Hash::check($request->current_password, Auth::user()->password)) {
                $res = User::find(Auth::id())->update(['password' => bcrypt($request->password)]);
                if ($res) {
                    $user = User::find(Auth::id());
                    return $this->sendResponse(new UserProfileResource($user), 'Password was changed successfully.');
                }
                return $this->sendError('Oops... Something went wrong, please try again.');
            } else {
                return $this->sendError('Oops... Incorrect Current Password, please try again.');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update_personal_info(Request $request)
    {
        request()->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email',
            'gender' => 'required|in:Male,Female',
            'birthdate' => 'nullable|date'
        ]);
        try {
            $payload = [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'middlename' => $request->middlename,
                'name' => $request->firstname . ' ' . $request->middlename . ' ' . $request->lastname,
                'email' => $request->email,
                'gender' => $request->gender,
                'birthdate' => $request->birthdate,
                'address' => $request->address,
                'mobile_num' => $request->mobile_number,
                'phone_num' => $request->phone_number,
            ];
            if ($request->isUploadImg) {
                $image = convertBase64ToImage($request->profile_path, "user-profile-" . time() . "-" . sprintf('%08d', Auth::id()) . "-" . date('Ymd'), 'user-profiles');
                $image_path = $image['path'];
                $payload['profile_path'] = $image_path;
            }

            $isExist = User::where('email', $request->email)->where('id', '!=', Auth::id())->count();
            if ($isExist) {
                return $this->sendError('Oops, Email has been used already.');
            }
            $user = User::find(Auth::id());
            if ($user->profile_path && $request->isUploadImg) {
                $imgExplodeUrl = explode("/", $user->profile_path);
                if (count($imgExplodeUrl)) {
                    $imgWithType = explode(".", end($imgExplodeUrl));
                    removeFileExist($imgWithType[0], 'user-profiles');
                }
            }
            $user->update($payload);
            if ($user) {
                return $this->sendResponse(new UserProfileResource($user), 'Profile Information was changed successfully.');
            }
            return $this->sendError('Oops... Something went wrong, please try again.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
