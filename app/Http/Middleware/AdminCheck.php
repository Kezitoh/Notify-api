<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle( $request, Closure $next)
    {
        // Pre-Middleware Action

        $id = $request->sub;
        if(!$user = User::findOrFail($id)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'El usuario especificado no existe.'
            ]);
        }
        if($user->id_role != 1){
          return response()->json([
              'status' => 'failed',
              'message' => 'El usuario especificado no es un administrador.'
          ]);
        }

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
