<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Type extends Model
{
    //

    public function getTypes() {
        if (isset($this->id)) {
            $type = DB::select("SELECT * FROM types WHERE id = $this->id AND is_active = 1");
            return $type;
        }

        $types = DB::select('SELECT * from types');

        return $types;
    }

}
