<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    //

    public function getGroups(Request $request) {
        $g = new Group();
        
        if($request->has('id')) {
            $g->id = ($request->id);
            return Group::where('id', $request->input('id'))->first();

        }
        
        $Groups = $g->getGroups();
        return $Groups;
    }

}
