<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use ResponseHelper;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        try{
            $requestData = $request->validated();
            $requestData['password'] = bcrypt($requestData['password']);
            $createdUser = User::create($requestData);

            return ResponseHelper::sendResponse(['user' => $createdUser], 200, 'Account Created, please login', false, []);

        }catch(\Exception $e){
            return ResponseHelper::sendResponse([], 500, $e->getMessage(), true, []);
        }
    }

    public function login(LoginRequest $request)
    {
        try{
            if (!Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                return ResponseHelper::sendResponse([], 400, 'Invalid email or password!', true, []);
            }

            $accessToken = Auth::user()->createToken('accessToken');
            
            return ResponseHelper::sendResponse([
                'user'      => auth()->user(),
                'token'     => $accessToken->plainTextToken
            ], 200, null, false, []);

        }catch(\Exception $e){
            return ResponseHelper::sendResponse([], 500, $e->getMessage(), true, []);
        }
    }

    public function logout(Request $request)
    {
        @Auth::user()->tokens()->delete();
        return ResponseHelper::sendResponse([], 204);
    }

}
