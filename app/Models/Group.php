<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    //
    public $timestamps=false;
    
    public function getGroups() {
        if (isset($this->id)) {
            $group = DB::select("SELECT * FROM groups WHERE id = $this->id");
            return $group;
        }

        $groups = DB::select('SELECT * FROM groups');

        return $groups;
    }

    public static function toggleActive($id, $value) {

        $res = DB::update("UPDATE groups SET is_active = $value WHERE id = $id");

        return $res;

    }

    public static function edit($id, $values) {

        $sql = "UPDATE groups g SET";
        $index =0;
        foreach($values as $key => $value) {
            if($index == count($values)-1) {
                $sql .= " g.$key = '$value'";
            }else {
                $sql .= " g.$key = '$value',";
            }
            $index++;
        }
        $sql .= " WHERE id = $id";
        
        $res = DB::update($sql);
        return $res;
    }

    public static function deletee($id) {
        $res = DB::delete("DELETE FROM groups WHERE id = $id");

        return $res;
    }
}
