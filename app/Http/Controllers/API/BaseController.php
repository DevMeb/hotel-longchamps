<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Services\LogService;

class BaseController extends Controller
{
    // success response method
    public function sendResponse($result, $message, $code = JsonResponse::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        LogService::info('Successful response sent.', ['message' => $message, 'data' => $result]);

        return response()->json($response, $code);
    }

    // return error response
    public function sendError($error, $errorMessages = [], $code = JsonResponse::HTTP_NOT_FOUND): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        LogService::error($error, ['details' => $errorMessages, 'status_code' => $code]);

        return response()->json($response, $code);
    }
}
