<?php

namespace App\Http\Controllers;

use App\Models\User_Notification;
use Illuminate\Http\Request;

class User_NotificationController extends Controller
{
    //
    public function setFavorite(Request $request)
    {

        if (
            !$request->has('notification_id') ||
            !$request->has('value') ||
            !$request->has('user_id')
        ) {
            return response()->json([
                'ok' => false,
                'message' => 'Falta información.'
            ]);
        }

        $res = User_Notification::setFavorite($request->notification_id,$request->sub, $request->value);
        if(!$res) {
            return response()->json([
                'ok' => false,
                'message' => 'Ha ocurrido un error actualizando la información'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Favorito añadido correctamente'
        ]);

    }

    public function read(Request $request) {

        if(!$request->has('id')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se especificó notificación'
            ]);
        }

        $res = User_Notification::setRead($request->id);

        if(!$res) {
            return response()->json([
                'ok' => false,
                'message' => 'Ha ocurrido un error con la operación'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'La operación se ha realizado satisfactoriamente'
        ]);

    }

    public function download(Request $request) {

        if(!$request->has('id')) {
            return response()->json([
                'ok' => false,
                'message' => 'No se especificó notificación'
            ]);
        }

        $res = User_Notification::setDownloaded($request->id);

        if(!$res) {
            return response()->json([
                'ok' => false,
                'message' => 'Ha ocurrido un error con la operación'
            ]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'La operación se ha realizado satisfactoriamente'
        ]);

    }

}
