<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private const TOKEN_NAME = 'api-token';

    public function __construct(private readonly ActivityLogService $activityLogService) {}

    public function register(array $payload): array
    {
        $user = User::query()->create([
            'first_name' => $payload['first_name'],
            'middle_name' => $payload['middle_name'] ?? null,
            'last_name' => $payload['last_name'],
            'email' => $payload['email'],
            'password' => $payload['password'],
            'name' => trim(implode(' ', array_filter([
                $payload['first_name'],
                $payload['middle_name'] ?? null,
                $payload['last_name'],
            ]))),
        ]);

        $token = $this->issueTokenFor($user);

        return [
            'message' => 'User registered.',
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials, ?Request $request = null): array
    {
        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials.',
            ];
        }

        $token = $this->issueTokenFor($user);

        $this->activityLogService->logAdminAuthActivity(
            $user,
            'auth.login',
            $request,
            ['token_name' => self::TOKEN_NAME]
        );

        return [
            'success' => true,
            'payload' => [
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token,
            ],
        ];
    }

    public function logout(?User $user, ?Request $request = null): void
    {
        if ($user?->currentAccessToken()) {
            $user->currentAccessToken()->delete();

            $this->activityLogService->logAdminAuthActivity(
                $user,
                'auth.logout',
                $request,
                ['token_name' => self::TOKEN_NAME]
            );
        }
    }

    private function issueTokenFor(User $user): string
    {
        return $user->createToken(self::TOKEN_NAME, $this->abilitiesFor($user))->plainTextToken;
    }

    private function abilitiesFor(User $user): array
    {
        return match ($user->role) {
            User::ROLE_ADMIN => ['manuscript.read', 'manuscript.write'],
            User::ROLE_SUPER_ADMIN => ['*'],
            default => ['manuscript.read'],
        };
    }
}
