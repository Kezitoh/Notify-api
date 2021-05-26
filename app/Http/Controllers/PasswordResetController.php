<?php

namespace App\Http\Controllers;

use App\Models\token_user;
use App\Models\User;
use DateTime;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Tymon\JWTAuth\Token;

class PasswordResetController extends Controller
{
    //

    public function sendResetCode(Request $request)
    {
    
        if(!$request->has('user')) {
            return response()->json([
                "ok" => false,
                "message" => "Especifique su usuario"
            ]);
        }
        $user_code = $request->user;

        $token = Str::random(6);

        $token_user = new token_user();
        
        $datetime = date_add(new Datetime(), date_interval_create_from_date_string('10 minutes'));
        
        $user = DB::connection('mysql')->table('users')->select('*')->where('user','=', $user_code)->limit(1)->get();
        if(empty($user[0])) {
            return response()->json([
                'ok' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }

        $token_user->id_user = $user[0]->id;
        
        $token_user->token = Hash::make($token);
        
        $token_user->expiration_date = $datetime;
        
        $token_user->save();

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = false;
        $mail->isSMTP();
        $mail->Host = 'mail.admyst.net';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@admyst.com';
        $mail->Password = '4eHA8DSv6[W[';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom('noreply@admyst.com', 'NO REPLY');
        $mail->addAddress($user[0]->email);
        $mail->isHTML(true);
        $mail->CharSet='UTF-8';
        $mail->Subject = 'Reestablecimiento de contraseña';
        $mail->Body  = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>
        
            <h1>Hola, '. $user[0]->name.'</h1>
        
            <p><h3>Hemos recibido una petición de reestablecimiento de contraseña.</h3></p>
            <p><h3>Si no has sido tú, simplemente ignora este mensaje.</h3></p>
            <br>
            <p>Para proceder a cambiar tu contraseña, pon el siguiente código en el campo indicado en la app: '.$token.'</p>
        
        </body>
        </html>';
        $mail->send();

        return response()->json([
            'ok' => true,
            'message' => 'Código de recuperación enviado'
        ]);

    }

    public function confirmReset(Request $request) {
        // dd($request->has('code'));
        if(!$request->has('code') || !$request->has('user') ) {
            return response()->json([
                "ok" => false,
                "message" => "Código y/o usuario no especificados."
            ], 400);
        }

        $code = $request->code;
        $user_user = $request->user;
        $user = DB::connection('mysql')->table('users')->select('id')->where('user', '=', $user_user)->get();
        $user_id = $user[0]->id;
        $now = new DateTime();
        $now_formatted =$now->format('Y/m/d H:m:s');
    
        $res = DB::select("SELECT tu.* FROM token_users tu JOIN users u on u.id = tu.id_user where id_user = $user_id AND expiration_date = (SELECT MAX(expiration_date) from token_users where id_user = $user_id) AND expiration_date > '$now_formatted' AND u.is_active = 1");
        if( empty($res[0]) || !Hash::check($code,$res[0]->token ) ) {
            return response()->json([
                'ok' => false,
                'message' => 'Confirmación fallida.'
            ]);
        }
        return response()->json([
            "ok" => true,
            'message' => 'Confirmación completada con éxito.'
        ]);

    }

    public function resetPassword(Request $request) {

        if($request->has('code')) {
            if( !$this->confirmReset($request)->getData()->ok ) {
                return $this->confirmReset($request)->throwResponse();
            }
        }

        $this->validate($request, [
            "password" => ["required","string","confirmed","min:6", "max:8", "regex:/([a-zA-Z][0-9]|[0-9][a-zA-Z])/"],
            "password_confirmation" => ["required","string","min:6", "max:8", "regex:/([a-zA-Z][0-9]|[0-9][a-zA-Z])/"],
            "user" => "required|string|max:9|min:9"
        ]);
        
        DB::connection('mysql')->table('users')->where('user','=',$request->user)->update(['password' => app("hash")->make($request->password)]);
        return response()->json([
            'ok' => true,
            'message' => 'Acción realizada exitosamente'
        ]);
    }

}
