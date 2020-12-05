<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApiAuthController extends Controller
{
    // register
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'date_of_birth' => 'required|date',
            'gender' => 'in:Male,Female',
            'avatar_url' => 'string'
        ]);

        if($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()],422);
        }

        // if (!is_dir(public_path(). '/upload/users/')) {
        //     // dir doesn't exist, make it
        //     mkdir(public_path(). '/upload/users/', 0775, true);
        // }

        $image_parts = explode(";base64,",$request['avatar_url']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $file = base64_decode($image_parts[1]);
        $safeName = rand(10000,120371). '.' . $image_type;
        Storage::disk('local')->put($safeName, $file);
        $request['avatar_url'] = $safeName;

        $request['password']=Hash::make($request['password']);
        $user = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];
        return response($response, 200);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6'
        ]);
        
        if($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()],422);
        }
        $user = User::where('email', $request->email)->first();
        if($user) {
            if(Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }

    public function logout(Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function getDetails(Request $request) {
        $user = $request->user();
        $file = Storage::disk('local')->get($user['avatar_url']);
        $type = pathinfo($user['avatar_url'], PATHINFO_EXTENSION);
        $user['avatar_url'] = 'data:image/' . $type . ';base64,' . base64_encode($file);
        $response = ['success' => $user];
        return response($response, 200);
    }
}
