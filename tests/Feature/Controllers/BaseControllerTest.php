<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController;
use App\Http\Services\LogService;
use Mockery;
use Mockery\MockInterface;

class BaseControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock LogService to prevent actual logging during the test
        $this->mock(LogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('info')->andReturnNull();
            $mock->shouldReceive('error')->andReturnNull();
        });
    }

    /**
     * Test the sendResponse method.
     */
    public function test_send_response()
    {
        $controller = new BaseController();

        $result = ['key' => 'value'];
        $message = 'Operation successful.';
        $response = $controller->sendResponse($result, $message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ], $response->getData(true));
    }

    /**
     * Test the sendResponse method with a custom status code.
     */
    public function test_send_response_with_custom_status_code()
    {
        $controller = new BaseController();

        $result = ['key' => 'value'];
        $message = 'Resource created.';
        $customStatusCode = JsonResponse::HTTP_CREATED;
        $response = $controller->sendResponse($result, $message, $customStatusCode);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($customStatusCode, $response->getStatusCode());
        $this->assertEquals([
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ], $response->getData(true));
    }

    /**
     * Test the sendError method.
     */
    public function test_send_error()
    {
        $controller = new BaseController();

        $error = 'Resource not found.';
        $response = $controller->sendError($error);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => $error,
        ], $response->getData(true));
    }

    /**
     * Test the sendError method with additional error messages.
     */
    public function test_send_error_with_additional_errors()
    {
        $controller = new BaseController();

        $error = 'Validation error.';
        $errorMessages = ['field' => 'Field is required.'];
        $response = $controller->sendError($error, $errorMessages);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => $error,
            'data'    => $errorMessages,
        ], $response->getData(true));
    }

    /**
     * Test the sendError method with a custom status code.
     */
    public function test_send_error_with_custom_status_code()
    {
        $controller = new BaseController();

        $error = 'Internal server error.';
        $customStatusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        $response = $controller->sendError($error, [], $customStatusCode);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($customStatusCode, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'message' => $error,
        ], $response->getData(true));
    }
}
