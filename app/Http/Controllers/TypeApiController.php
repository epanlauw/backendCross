<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Type;

class TypeApiController extends BaseController
{
    public function index(Request $request) {
        $type = Type::all();
        $success['type'] = $type;
        return $this->sendResponse($success, "Show all types");
    }

    public function create (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'string'
        ]);

        if($validator->fails()) {
            return $this->sendError("Validation Error.", $validator->errors(), 422);
        }

        $type = Type::create($request->toArray());

        $success['type'] = $type;
        
        return $this->sendResponse($success, "Type created successfully.");
    }
}
