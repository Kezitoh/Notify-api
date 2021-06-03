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

    /* Devuelve un json con el token */
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

    /** Crea un usuario nuevo */
    public function register(Request $request) {

        $this->validate($request, [
            "user" => "required|string|max:9|min:9|unique:users",
            // "password" => "required|string|confirmed|min:6",
            // "password_confirmation" => "required|string|min:6",
        ]);

        try{
            $userCtrl = new UserController();
            $user = $userCtrl->create($request);
            if(!$user) {
        
                return response()->json([
                    'ok' => false,
                    'message' => 'ERROR CREATING USER'
                ]);
            }
            return response()->json([
                'ok' => true,
                "user" => $user, 
                "message" => "CREATED"
            ], 201);

        }catch (Exception $e) {
            return response()->json([
                'ok' => false,
                "error" => $e->getMessage(),
                "message" => "User registration failed."
            ]);
        }

    }

    /** Valida los credenciales del usuario y le otorga un token si son correctos */
    public function login(Request $request) {
        $this->validate($request, [
            "user" => "required|string",
            "password" => "required|string"
        ]);

        $credentials = $request->only(["user", "password"]);

        if((!$token = Auth::attempt($credentials) )|| (!User::checkUserActive($request->user))) {
            //login failed
            return response()->json([
                'ok' => false,
                "message" => "Unauthorized"],200);
        }

        User::setOnline($request->user);

        return $this->respondWithToken($token);
    }

    /* Llama a setOffline y devuelve una respuesta */ 
    public function logout(Request $request) {
        
        User::setOffline($request->user_id);
        // Auth::guard()->logout();
        return response()->json([
            'ok' => true,
            "message" => "User succesfully signed out"]);
    }

    /* Refresca el token para que no caduque */
    public function refresh(Request $request) {
        return $this->respondWithToken(auth()->refresh());
    }

    /** Devuelve información del usuario logeado por via del JWT */
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
            "email" => $user->email,
            "has_logged" => $user->has_logged
        ]);
    }

}
