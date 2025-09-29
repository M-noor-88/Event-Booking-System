<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Register user and return ['user' => User, 'token' => string]
     */
    public function register(array $payload): array
    {
        $payload['password'] = Hash::make($payload['password']);

        $payload['role'] = $payload['role'] ?? 'customer';

        $user = $this->users->create($payload);

        // Create Sanctum token
        $token = $user->createToken('api_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Attempt login, returns ['user'=>User, 'token'=>string] or throws ValidationException
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        // Attempt authentication
        if (! auth()->attempt($credentials)) {
            throw ValidationException::withMessages(['email' => ['Invalid credentials']]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Create token
        $token = $user->createToken('api_token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Logout current token
     */
    public function logout(\Illuminate\Http\Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }

    public function me()
    {
        return Auth::user();
    }
}
