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

    public function create(Request $request) {

        

        if( !$request->has('name') || !$request->has('description') ) {
            return response()->json([
                'ok' => false,
                'message' => "Nombre o descripción no especificados."
            ]);
        }

        $group = new Group();

        $group->name = $request->name;

        $group->description = $request->description;

        $group->save();

        return response()->json([
            'ok' => true,
            'message' => 'Grupo creado con éxito.'
        ]);

    }

}
