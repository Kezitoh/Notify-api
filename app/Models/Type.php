<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Type extends Model
{
    //

    public $timestamps = false;

    public function getTypes() {
        if (isset($this->id)) {
            $type = DB::select("SELECT * FROM types WHERE id = $this->id");
            return $type;
        }

        $types = DB::select('SELECT * FROM types');

        return $types;
    }

    public static function toggleActive($id, $value) {

        $res = DB::update("UPDATE types SET is_active = $value WHERE id = $id");

        return $res;

    }

    public static function edit($id, $values) {

        $sql = "UPDATE types t SET";
        $index =0;
        foreach($values as $key => $value) {
            if($index == count($values)-1) {
                $sql .= " t.$key = '$value'";
            }else {
                $sql .= " t.$key = '$value',";
            }
            $index++;
        }
        $sql .= " WHERE id = $id";
        
        $res = DB::update($sql);
        return $res;
    }

    public static function deletee($id) {
        $res = DB::delete("DELETE FROM types WHERE id = $id");

        return $res;
    }

}
