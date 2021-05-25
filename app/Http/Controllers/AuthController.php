<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Claims\Collection;
use Tymon\JWTAuth\Claims\Factory;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Payload;
use Tymon\JWTAuth\Token;
use Tymon\JWTAuth\Validators\PayloadValidator;

class AuthController extends Controller
{
    //
    public function __construct()
    {
        // Remove the middleware on the login and register routes
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function respondWithToken($token)
    {
        
        // Return a token response to the user
        return response()->json([
            'ok' => true,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 3600
        ], 200);
    }

    public function register(Request $request) {

        $this->validate($request, [
            "user" => "required|string|max:9|min:9|unique:users",
            // "password" => "required|string|confirmed|min:6",
            // "password_confirmation" => "required|string|min:6",
        ]);

        try{
            $request->request->add(['password' => 'prueba']);
            $userCtrl = new UserController();
            $user = $userCtrl->create($request);
            if(!$user) {
        
                return response()->json([
                    'ok' => false,
                    'message' => 'ERROR CREATING USER'
                ]);
            }
            return response()->json([
                "user" => $user, 
                "message" => "CREATED"
            ], 201);

        }catch (Exception $e) {
            return response()->json([
                "message" => "User registration failed.",
                "error" => $e->getMessage()
            ]);
        }

    }

    public function login(Request $request) {
        $this->validate($request, [
            "user" => "required|string",
            "password" => "required|string"
        ]);

        $credentials = $request->only(["user", "password"]);

        if(!$token = Auth::attempt($credentials)) {
            //login failed
            return response()->json([
                'ok' => false,
                "message" => "Unauthorized"],200);
        }

        return $this->respondWithToken($token);
    }

    public function logout() {
        Auth::guard()->logout();
        return response()->json([
            'ok' => true,
            "message" => "User succesfully signed out"]);
    }

    public function refresh(Request $request) {
        return $this->respondWithToken(auth()->refresh());
    }

    public function me(Request $request) {

        $id = $request->sub;
        $user = User::findOrFail($id);

        return response()->json([
            "id" => $user->id,
            "id_role" => $user->id_role,
            "id_group" => $user->id_group,
            "user" => $user->user,
            "name" => $user->name,
            "surname" => $user->surname,
            "email" => $user->email
        ]);
    }

}
