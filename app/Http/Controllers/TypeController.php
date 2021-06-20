<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    //

    public function getTypes(Request $request)
    {
        $t = new Type();

        if ($request->has('id')) {
            $t->id = ($request->id);
            return Type::where('id', $request->input('id'))->first();
        }

        $Types = $t->getTypes();
        return $Types;
    }

    public function create(Request $request)
    {



        if (!$request->has('name') || !$request->has('description')) {
            return response()->json([
                'ok' => false,
                'message' => "Nombre o descripción no especificados."
            ]);
        }

        $type = new Type();

        $type->name = $request->name;

        $type->description = $request->description;

        if ($request->has('active')) {
            $type->is_active = $request->active;
        }

        $type->save();

        return response()->json([
            'ok' => true,
            'message' => 'Tipo creado con éxito.'
        ]);
    }

    public function delete(Request $request)
    {

        if (!$request->has('id')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se ha especificado el tipo'
            ]);
        }

        $res = Type::deletee($request->id);
        if (!$res) {

            return response()->json([
                'ok' => false,
                'message' => 'La operación no se ha realizado correctamente'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Tipo borrado correctamente'
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

        $res = Type::toggleActive($request->id, $request->value);

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

        $res = Type::edit($request->id, $request->values);

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
