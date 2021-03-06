<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function getUsers(Request $request) {

        $u = new User();
        
        if($request->has('id')) {
            $u->id = $request->id;
            $res = $u->getUsers();
            return response()->json([
                'ok' => true,
                'users' => $res
            ]);
        }else if($request->has('group')) {
            $u->group = $request->group;
            $res = $u->getUsers();
            return response()->json([
                'ok' => true,
                'users' => $res
            ]);
        }
        
        $users = $u->getUsers();
        return response()->json([
            'ok' => true,
            'users' => $users
        ]);

    }

    public function create(Request $request) { 

        if( 
            !$request->has('id_group')||
            !$request->has('id_role')||
            !$request->has('user')||
            !$request->has('name')||
            !$request->has('surname')||
            !$request->has('password')
        ) {
            return false;
        }

        $user = new User();        

        $user->id_group = $request->input('id_group');
        $user->id_role = ($request->input('id_role'));
        $user->user = $request->input("user");
        $plainPassword = $request->input("password");
        $user->password = Hash::make($plainPassword,['rounds' => 13]);
        $user->name = ($request->input('name'));
        $user->surname = ($request->input('surname'));
        $request->has('email')? $user->email = ($request->input('email')) : null;
        $request->has('is_online')? $user->is_online = $request->input('is_online') : $user->is_online = 0;
        $request->has('is_active')? $user->is_active = $request->input('is_active') : $user->is_online = 1;

        // dd($user->is_online);

        $user->save();

        return $user;



    }
    

    public function delete(Request $request)
    {

        if (!$request->has('id')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se ha especificado el usuario'
            ]);
        }

        $res = User::deletee($request->id);
        if (!$res) {

            return response()->json([
                'ok' => false,
                'message' => 'La operaci??n no se ha realizado correctamente'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'usuario borrado correctamente'
        ]);
    }

    public function toggleActive(Request $request)
    {

        if (!$request->has('id') || !$request->has('value')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se han especificado todos los datos'
            ]);
        }

        $res = User::toggleActive($request->id, $request->value);

        if (!$res) {

            return response()->json([
                'ok' => false,
                'message' => 'La operaci??n no se ha realizado correctamente'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'El registro se ha actualizado correctamente'
        ]);
    }

    public function edit(Request $request) {
        if(!$request->has('id')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se ha especificado el tipo'
            ]);
        }

        $res = User::edit($request->id, $request->values);

        if (!$res) {

            return response()->json([
                'ok' => false,
                'message' => 'La operaci??n no se ha realizado correctamente'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'El registro se ha actualizado correctamente'
        ]);

    }


}
