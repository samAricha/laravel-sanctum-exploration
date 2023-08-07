<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;
    public function register(StoreUserRequest $request)
    {
//        $request->validated($request->only(['name', 'email', 'password']));
        $request->validated($request->all());


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ]);




//        return $this->success([
//            'user' => $user,
//            'token' => $user->createToken('auth_token')->plainTextToken
//        ]);

//        $token = $user->createToken('auth_token')->plainTextToken;
//        return response()->json([
//            'access_token' => $token,
//            'token_type' => 'Bearer',
//        ]);
    }


    public function login(LoginUserRequest $request)
    {
        $request->validated($request->only(['email', 'password']));
        if(!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'message' => 'You have succesfully been logged out and your token has been removed'
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }


}
