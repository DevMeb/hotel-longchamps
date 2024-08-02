<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Models\Tutor;
use App\Http\Resources\TutorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TutorController extends BaseController
{
    // Get all tutors
    public function index(): JsonResponse
    {
        try {
            // Fetch all tutors
            $tutors = Tutor::all();

            // Return a success response with the tutors data
            return response()->json([
                'success' => true,
                'message' => 'Tutors retrieved successfully.',
                'data' => TutorResource::collection($tutors)
            ], 200);
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            Log::error('An error occurred: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving tutors.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store a newly created resource in storage.
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required:email',
            'phone' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $tutor = Tutor::create($input);

        return $this->sendResponse(new TutorResource($tutor), 'Tutor created successfully.');
    }

    // Display the specified resource.
    public function show($id): JsonResponse
    {
        $tutor = Tutor::find($id);

        if (is_null($tutor)) {
            return $this->sendError('Tutor not found.');
        }

        return $this->sendResponse(new TutorResource($tutor), 'Tutor retrieved successfully.');
    }

    // Update the specified resource in storage.
    public function update(Request $request, Tutor $tutor): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required:email',
            'phone' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $tutor->first_name = $input['first_name'];
        $tutor->last_name = $input['last_name'];
        $tutor->email = $input['email'];
        $tutor->phone = $input['phone'];
        $tutor->save();

        return $this->sendResponse(new TutorResource($tutor), 'Tutor updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Tutor $tutor): JsonResponse
    {
        $tutor->delete();

        return $this->sendResponse([], 'Tutor deleted successfully.');
    }
}