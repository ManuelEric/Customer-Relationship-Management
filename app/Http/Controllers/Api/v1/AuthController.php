<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Authorization\AuthorizationService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    public function login(
        Request $request,
        AuthorizationService $authorizationService,
    )
    {
        $credentials = $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required',
        ]);

        $credentials = $request->only(['email', 'password']);

        # check credentials
        if (!Auth::attempt($credentials, true)) 
            return response()->json([ 'password' => 'Wrong email or password' ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        
        try {

            $user = Auth::user();
            $userId = $user->id;
            $authorizationService->checkPermissionFromUserType($userId);
            $scopes = $authorizationService->checkUserRole($user);
            [$generatedToken, $acceptableUserRole] = $authorizationService->authorize($user, $scopes);

        } catch (\Exception $e) {

            throw new HttpResponseException(
                response()->json([
                    'errors' => $e->getMessage()
                ])
            );

        }

        return response()->json([
            'token' => $generatedToken,
            'data' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        // $tokenId = $request->user()->token()->id;
        $tokenId = auth()->guard('api')->user()->token()->id;
        Log::debug('Token ID has request to be revoked' . $tokenId);

        // Revoke an access token...
        $tokenRepository->revokeAccessToken($tokenId);
        
        // Revoke all of the token's refresh tokens...
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

        return true;
    }
}
