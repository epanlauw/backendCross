<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApiAuthController extends BaseController
{
    // register
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:6',
            'date_of_birth' => 'required|date',
            'gender'        => 'in:Male,Female',
            'avatar_url'    => 'string'
        ]);

        if($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors(), 422);
        }

        // TODO: Refactor this to be more readable
        $image_parts = explode(";base64,",$request['avatar_url']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $file = base64_decode($image_parts[1]);
        $safeName = rand(10000,120371). '.' . $image_type;
        Storage::disk('local')->put($safeName, $file);
        $request['avatar_url'] = $safeName;

        $request['password']=Hash::make($request['password']);
        $user = User::create($request->toArray());

        $success = [
            'token' => $user->createToken('Laravel Password Grant Client')->accessToken,
            'name'  => $user->first_name . ' ' . $user->last_name
        ];

        return $this->sendResponse($success, "User created successfully.");
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|string|email|max:255',
            'password'  => 'required|string|min:6'
        ]);
        
        if($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if($user) {
            if(Hash::check($request->password, $user->password)) {
                $success['token'] = $user->createToken('Laravel Password Grant Client')->accessToken;
                return $this->sendResponse($success, "User login successfully.");
            } else {
                return $this->sendError("Login Error.", ["Password mismatch."], 422);
            }
        } else {
            return $this->sendError("Login Error.", ["User does not exist."], 422);
        }
    }

    public function logout(Request $request) {
        $token = $request->user()->token();
        $revoke_token = $token->revoke();

        if(!$revoke_token) {
            return $this->sendError("Logout Error.", ["Failed to log out."], 422);
        }

        $success['token_revoked'] = $revoke_token;
        
        return $this->sendResponse($success, "Successfully logged out.");
    }

    public function getDetails(Request $request) {
        $user = $request->user();
        $file = Storage::disk('local')->get($user['avatar_url']);
        $type = pathinfo($user['avatar_url'], PATHINFO_EXTENSION);
        $user['avatar_url'] = 'data:image/' . $type . ';base64,' . base64_encode($file);
        
        $success['user'] = $user;

        return $this->sendResponse($success, "User details received successfully.");
    }
}
