<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\JsonResponseTrait;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

// we'll add LoginRequest below

class AuthController extends Controller
{
    use JsonResponseTrait;

    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(RegisterRequest $request) : JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->register($data);

        return $this->successResponse([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 'Registered', 201);
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $credentials = $request->validated();

        try {
            $result = $this->service->login($credentials);
        } catch (ValidationException $e) {
            return $this->errorResponse('Invalid credentials', 401, $e->errors());
        }

        return $this->successResponse([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 'Logged in');
    }

    public function logout(Request $request) : JsonResponse
    {
        $this->service->logout($request);
        return $this->successResponse(null, 'Logged out');
    }

    public function me(): JsonResponse
    {
        try {
            $data = $this->service->me();
            return $this->successResponse($data);
        } catch (Exception $e) {
            Log::error('Error fetching user data: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch user data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
