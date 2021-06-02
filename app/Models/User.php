<?php

namespace App\Models;

use DateTime;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;


    protected $fillable = [
        'user', 'password'
    ];

    protected $hidden = [
        //
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user' => $this->user,
            'id_role' => $this->id_role,
        ];
    }



    public function getUsers()
    {
        if (isset($this->id)) {
            $user = DB::select("SELECT * FROM users WHERE id = $this->id");
            return $user;
        }

        $users = DB::select('SELECT * from users');

        return $users;
    }

    public static function setOnline($user) {
        
        DB::update("UPDATE users SET is_online = 1 WHERE user = '$user'");

    }

    public static function setOffline($user)  {
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');
        DB::update("UPDATE users SET is_online = 0, last_online = '$date' WHERE user ='$user'");

        return "set offline";

    }

    public static function checkUsersNotifications($data)
    {
        $user = $data["id_user"];
        $notif = $data["id_notification"];
        $check = DB::select("SELECT id_user, id_notification FROM users_notifications WHERE id_notification = $notif AND id_user = $user");
        if (!empty($check)) {
            return false;
        }
        return true;
    }

    // public static function checkUserValid($user) {
    //     if(User::checkUserActive($user) &&
    //     User::checkUserLogged($user)) {
    //         return true;
    //     }
    //     return false;
    // }

    public static function checkUserActive($user) {
        $res = DB::select("SELECT is_active, is_online FROM users WHERE user = '$user';");
        if($res[0]->is_active == 1) {
            return true;
        }
        return false;
    }

    // public function createe() {
    //     $hashed_pwd = Hash::make($this->password, ['rounds' => 13]);
    //     $res = DB::connection('mysql')->table('users')->insert(['id_role' => $this->id_role, 'id_group' => $this->id_group, 'user' => $this->user, 'name' => $this->name, 'surname' => $this->surname, 'password' => $hashed_pwd, 'email' => $this->email]);

    //     return $res;
    // }

    public static function checkUserLogged($user) {
        $res = DB::select("SELECT has_logged FROM users WHERE user = '$user';");
        if($res[0]->has_logged == 1) {
            return true;
        }
        return false;
    }

    public static function toggleActive($id, $value) {

        $res = DB::update("UPDATE users SET is_active = $value WHERE id = $id");

        return $res;

    }

    public static function edit($id, $values) {

        $sql = "UPDATE users u SET";
        $index =0;
        foreach($values as $key => $value) {
            if($index == count($values)-1) {
                $sql .= " u.$key = '$value'";
            }else {
                $sql .= " u.$key = '$value',";
            }
            $index++;
        }
        $sql .= " WHERE id = $id";
        
        $res = DB::update($sql);
        return $res;
    }

    public static function deletee($id) {
        $res = DB::delete("DELETE FROM users WHERE id = $id");

        return $res;
    }



}
