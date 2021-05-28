<?php

namespace App\Models;

use App\Providers\UtilServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{

    public function __construct()
    {
    }


    public function getNotifications()
    {
        $sql = "SELECT n.*, t.name as nametype FROM notifications n JOIN types t ON t.id = n.id_type";
        if (isset($this->id)) {
            $sql .= " WHERE id = $this->id";
            $notifications = DB::select($sql);
        }
        $notifications = DB::select($sql); // Si no se le pasa nada, simplemente devuelve todas las filas
        return $notifications;
    }

    public function create()
    {

        $notification = DB::table('notifications')->insertGetId(
            array('id_type' => $this->type, 'creator' => $this->creator, 'title' => $this->title, 'text' => $this->text, /*'end_time' => Carbon::now() + TIEMPO_DE_VIDA ,*/ 'attachment' => $this->attachment, 'is_active' => $this->is_active)
        );

        return $notification;
    }

    public function send($data)
    {
        // if($data['id_user'] == ''){
        //     return false;

        // }

        $insert = DB::connection('mysql')->table('users_notifications')->insert($data);
        return $insert;
    }


    public function sendToGroup($group_id)
    {
        if (empty($group_id)) {
            return false;
        }
        $user_ids = DB::select("SELECT u.id FROM users u JOIN groups g on u.id_group=g.id WHERE u.id_group = $group_id");

        $sql_compuesta = [];
        for ($i = 0; $i < count($user_ids); $i++) {
            $user_id = $user_ids[$i]->id;

            $data = ['id_notification' => $this->id, 'id_user' => $user_id];
            if (User::checkUsersNotifications($data)) {

                array_push($sql_compuesta, $data);

            }
        }

        return $this->send($sql_compuesta);
    }


    public function getNotificationsByUser($user_id)
    {

        $sql ="SELECT n.*, t.name as nametype FROM notifications n " .
        "JOIN users_notifications un ON n.id = un.id_notification " .
        "JOIN users u ON un.id_user = u.id " .
        "JOIN types t ON t.id = n.id_type ".
        "WHERE u.id = $user_id AND n.is_active = 1 ";

        if(isset($this->filters)) {

            $filter = $this->filters;
            
            $filter = join(",",$filter);
            $sql .= " AND t.id IN ($filter) ORDER BY n.created DESC";
            $notifications = DB::select($sql);
            return $notifications;
            
        }
        $sql .= "ORDER BY n.created DESC";

        $notifications = DB::select($sql);


        return $notifications;
    }


    public function getNotificationsByCreator($creator) {
        
        $sql = "SELECT n.*, t.name as nametype FROM notifications n " .
        "JOIN users u ON n.creator = u.id " .
        "JOIN types t ON t.id = n.id_type ".
        "WHERE u.id = $creator AND n.is_active = 1 "; 
        
        if(isset($this->filters)) {

            $filter = $this->filters;
            
            $filter = join(",",$filter);
            $sql .= " AND t.id IN ($filter) ORDER BY n.created DESC";
            $notifications = DB::select($sql);
            return $notifications;

        }
        $sql .= "ORDER BY n.created DESC";
        $notifications = DB::select($sql);

        return $notifications;
        
    }


    public function getNotificationsByGroup($group_id)
    {

        $res = DB::select("SELECT DISTINCT n.* FROM users_notifications un JOIN users u ON u.id = un.id_user" .
            " JOIN notifications n ON n.id = un.id_notification" .
            " WHERE u.id_group = $group_id" .
            " AND n.id = ALL (SELECT un.id_notification FROM users_notifications un
         JOIN users u ON u.id = un.id_user
         JOIN groups g on u.id_group=g.id WHERE u.id_group = $group_id)"); // TODO: Conseguir un buen resultado consistente

        // $result = DB::connection('mysql')
        // ->table('users_notifications as un')
        // ->join('users as u','u.id','=','un.id_user')
        // ->join('notifications as n','n.id','=','un.id_notification')
        // ->where('u.id_group','=',$group_id)
        // ->dd();


        return $res;
    }
}
