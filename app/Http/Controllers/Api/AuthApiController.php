<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthApiController extends Controller
{
    /**
     * POST /api/v1/auth/login
     * Returns a JWT access token on successful login.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'nip'      => 'required|string',
            'password' => 'required|string',
        ]);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'NIP atau password salah.',
            ], 401);
        }

        return $this->tokenResponse($token);
    }

    /**
     * POST /api/v1/auth/logout
     * Invalidates the current JWT token.
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Berhasil logout.']);
    }

    /**
     * POST /api/v1/auth/refresh
     * Returns a new JWT token (old token is invalidated).
     */
    public function refresh(): JsonResponse
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return $this->tokenResponse($token);
    }

    /**
     * GET /api/v1/auth/me
     * Returns the authenticated user's profile.
     */
    public function me(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'id'            => $user->id,
            'nip'           => $user->nip,
            'name'          => $user->name,
            'email'         => $user->email,
            'no_telp'       => $user->no_telp,
            'instansi'      => $user->instansi,
            'is_superadmin' => $user->is_superadmin,
        ]);
    }

    /**
     * Build the standard token response payload.
     */
    private function tokenResponse(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => config('jwt.ttl') * 60,
        ]);
    }
}
