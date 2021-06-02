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

        if ($request->has('active')) {
            $group->is_active = $request->active;
        }

        $group->save();

        return response()->json([
            'ok' => true,
            'message' => 'Grupo creado con éxito.'
        ]);

    }

    public function delete(Request $request)
    {

        if (!$request->has('id')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se ha especificado el Grupo'
            ]);
        }

        $res = Group::deletee($request->id);
        if (!$res) {

            return response()->json([
                'ok' => false,
                'message' => 'La operación no se ha realizado correctamente'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Grupo borrado correctamente'
        ]);
    }

    public function toggleActive(Request $request)
    {

        if (!$request->has('id') || !$request->has('value')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se han especificado todos los datos'
            ]);
        }

        $res = Group::toggleActive($request->id, $request->value);

        if (!$res) {

            return response()->json([
                'ok' => false,
                'message' => 'La operación no se ha realizado correctamente'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'El registro se ha actualizado correctamente'
        ]);
    }

    public function edit(Request $request) {
        if(!$request->has('id')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se ha especificado el tipo'
            ]);
        }

        $res = Group::edit($request->id, $request->values);

        if (!$res) {

            return response()->json([
                'ok' => false,
                'message' => 'La operación no se ha realizado correctamente'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'El registro se ha actualizado correctamente'
        ]);

    }

}
