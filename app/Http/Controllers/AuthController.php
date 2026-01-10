<?php

namespace App\Http\Controllers;

use App\Core\Application\Commands\RegisterCommand;
use App\Core\Application\Commands\LoginCommand;
use App\Core\Application\DTOs\RegisterInputDTO;
use App\Core\Application\DTOs\LoginInputDTO;
use App\Core\Domain\Exceptions\InvalidCredentialsException;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Throwable;

/**
 * AuthController - handles authentication endpoints (register, login).
 * Delegates business logic to commands and returns formatted responses.
 */
class AuthController
{
    /**
     * Register a new user account.
     * Creates user and issues initial access token.
     *
     * @param Request $request HTTP request
     * @param RegisterCommand $command Register use case
     * @return JsonResponse User data with access token
     */
    public function register(Request $request, RegisterCommand $command): JsonResponse
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|min:3|max:255|unique:users',
                'email' => 'required|email:rfc,dns|unique:users',
                'password' => [
                    'required',
                    Password::min(8)
                        ->mixedCase()
                        ->letters()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),
                ],
            ]);

            $dto = new RegisterInputDTO(
                username: $validated['username'],
                email: $validated['email'],
                plainPassword: $validated['password']
            );

            $result = $command->execute($dto);

            return response()->json(
                new AuthResource($result),
                201
            );
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Authenticate user and issue access token.
     *
     * @param Request $request HTTP request
     * @param LoginCommand $command Login use case
     * @return JsonResponse User data with access token
     */
    public function login(Request $request, LoginCommand $command): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email:rfc,dns',
                'password' => 'required|string',
                'remember_me' => 'nullable|boolean',
            ]);

            $dto = new LoginInputDTO(
                email: $validated['email'],
                plainPassword: $validated['password'],
                rememberMe: (bool) ($validated['remember_me'] ?? false)
            );

            $result = $command->execute($dto);

            return response()->json(
                new AuthResource($result),
                200
            );
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'message' => 'Invalid credentials',
                'error' => $e->getMessage(),
            ], 401);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

