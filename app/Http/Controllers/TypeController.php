<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    //

    public function getTypes(Request $request) {
        $t = new Type();
        
        if($request->has('id')) {
            $t->id = ($request->id);
            return Type::where('id', $request->input('id'))->first();

        }
        
        $Types = $t->getTypes();
        return $Types;
    }

}
