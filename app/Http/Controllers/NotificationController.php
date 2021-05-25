<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Providers\UtilServiceProvider;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    //



    public function getNotifications(Request $request) // Obtiene notificaciones en función de los parámetros pasados.
    {


        $n = new Notification();



        $has_id = $request->has('id');
        $has_user = $request->has('user');
        $has_group = $request->has('group');




        if ($has_user) { // Si se le pasa un usuario traerá todas las notificaciones del usuario
            $user = $request->input('user');
            $notifications = $n->getNotificationsByUser($user);
            return $notifications;
        } else if ($has_group) { // Si se le pasa un grupo traerá todas las notificaciones que los usuarios de un mismo grupo tengan todos en común (Work In Progress)
            $group = $request->input('group');
            $notifications = $n->getNotificationsByGroup($group);
            return $notifications;
        } else if ($has_id) { // Si se le pasa una id traerá la notificación que le corresponda
            $n->setId($request->id);
            $notification = $n->getNotifications();
            return $notification;
        }

        $notifications = $n->getNotifications();
        return $notifications;
    }





    public function create(Request $request) // Crea una notificación y si se le pasa además un grupo y/o un usuario llama a la función send
    {



        $type = $request->input('type');
        $text = $request->input('text');
        $title = $request->input('title');

        if (empty($type) || empty($title) || empty($text)) {
            return false;
        }

        $n = new Notification();

        $n->setAttachment($request->has('attachment') && $request->attachment != '' ? $request->input('attachment') : null);
        $n->setIsActive($request->has('is_active') ? $request->input('is_active') : 1);

        $n->setType($type);
        $n->setTitle($title);
        $n->setText($text);

        $res = $n->create();

        if (!$request->has('user') && !$request->has('group')) {
            return $res;
        }
        if ($request->has('user')) {
            $request->request->add(['notification' => $res]);
        }
        if ($request->has('group')) {
            $request->request->add(['notification' => $res]);
        }
        return $this->send($request);
    }




    public function send(Request $request) // Envía una notificación a uno o varios grupos/usuarios
    {

        if (!$request->has('notification') || (!$request->has('user') || $request->user == ['']) && ($request->group == [''] || !$request->has('group'))) {
            return response()->json([
                'ok' => false,
                'message' => 'Request inválida'
            ]);
        }

        $n = new Notification();


        $id_notif = $request->input('notification');

        $n->setId($id_notif);

        if ($request->has('user') && ($request->user != [''] || $request->user != '')) {

            $id_users = $request->user;
            //dd($id_users);

            if (is_array($id_users)) {

                $sql_concat = [];

                for ($i = 0; $i < count($id_users); $i++) {
                    $id_user = $id_users[$i];
                    $data = ['id_notification' => $id_notif, 'id_user' => $id_user];
                    if(User::checkUsersNotifications($data)) {
                        
                        array_push($sql_concat, $data);
        
                    }
                }

                $n->send($sql_concat);
            } else {
                $sql = ['id_notification' => $id_notif, 'id_user' => $id_users];
                    if(User::checkUsersNotifications($sql)) {
                        $n->send($sql);
                    }
                
            }
        }

        if ($request->has('group') &&  $request->group != ['']) {

            $groups_id = $request->input('group');

            if (is_array($groups_id)) {

                for ($i = 0; $i < count($groups_id); $i++) {
                    $group_id = $groups_id[$i];
                    $n->sendToGroup($group_id);
                }
            } else {
                $n->sendToGroup($groups_id);
            }
        }
        return response()->json([
            'ok' => true,
            'message' => 'Terminado'
        ]);
    }


    public function downloadAttachment(Request $request)
    {
        // $request->has
    }
}