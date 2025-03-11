<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Authorization\AuthorizationService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
