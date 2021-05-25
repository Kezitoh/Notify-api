<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;



class UserController extends Controller
{
    //

    public function getUsers(Request $request) {

        $u = new User();
        
        if($request->has('id')) {
            $u->id = ($request->id);
            return User::where('id', $request->input('id'))->first();

        }
        
        $users = $u->getUsers();
        return $users;

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
        $user->password = app("hash")->make($plainPassword);
        $user->name = ($request->input('name'));
        $user->surname = ($request->input('surname'));
        $request->has('email')? $user->email = ($request->input('email')) : null;
        $request->has('is_online')? $user->is_online = $request->input('is_online') : $user->is_online = 0;
        $request->has('is_active')? $user->is_active = $request->input('is_active') : $user->is_online = 1;

        // dd($user->is_online);

        $user->save();

        return $user;



    }
    


}
