<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User_Notification extends Model
{
    //
    public function __construct()
    {
        
    }

    public static function setFavorite($notif_id, $user_id, $value)
    {
        $sql = "UPDATE users_notifications SET fav = $value WHERE id_notification = $notif_id AND id_user = $user_id";
        
        $res = DB::update($sql);

        return $res;
    }

    public static function setRead($id) {

        $now = date('Y-m-d H:i:s');

        $res = DB::update("UPDATE users_notifications SET is_read = 1, datetime_read = '$now' WHERE id = $id");
    
        return $res;

    }

    public static function setDownloaded( $id ) {

        $now = date('Y-m-d H:i:s');

        $res = DB::update("UPDATE users_notifications SET is_downloaded = 1, datetime_downloaded = '$now' WHERE id = $id");

        return $res;

    }

}
