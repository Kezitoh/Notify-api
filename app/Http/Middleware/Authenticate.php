<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Auth\Factory as Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!$request->hasHeader('x-token')) {
            return response('Token not provided.', 401);
        }
        $token = $request->header('x-token');

        try{
            $decoded = JWTAuth::decode(new Token($token));

        }catch(TokenInvalidException $e) {
            return response()->json([
                "message" => "The token is invalid.",
                "error" => $e->getMessage()
            ]);
        }catch(Exception $e) {
            return response()->json([
                "message" => "There was an error authenticating the token.",
                "error" => $e->getMessage()
            ]);
        }

        $sub = $decoded["sub"];
        $user = $decoded["user"];
        $id_role = $decoded["is_role"];

        $request->request->add(["sub" => $sub, "user_id" => $user, "id_role" => $id_role]);

        return $next($request);
    }
}
