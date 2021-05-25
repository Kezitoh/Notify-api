<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Group extends Model
{
    //
    public function getGroups() {
        if (isset($this->id)) {
            $group = DB::select("SELECT * FROM groups WHERE id = $this->id");
            return $group;
        }

        $groups = DB::select('SELECT * from groups');

        return $groups;
    }
}
