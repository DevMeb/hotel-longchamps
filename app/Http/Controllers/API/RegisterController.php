<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    // Register api
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);

            // If validation fails, return an error response
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Hash the password and create the user
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            // Generate an API token for the user
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
                'data' => $success
            ], 201);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while registering the user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Login api
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['name' => $request->name, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}