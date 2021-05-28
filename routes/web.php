<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->group(['middleware' => 'auth'], function () use ($router) {

    $router->get("/users", 'UserController@getUsers');
    $router->get('/types', 'TypeController@getTypes');
    $router->get('/groups', 'GroupController@getGroups');
    $router->get("/notifications", 'NotificationController@getNotifications');
    
    $router->get("/download", "FileController@downloadFile");
    
    $router->group(['middleware' => 'admin'], function() use ($router){
        
        $router->post("/users/create", 'UserController@create');
        
        $router->post("/notifications/create", 'NotificationController@create');

        $router->post("/notifications/delete", 'NotificationController@delete');
        
        $router->post("/notifications/send", 'NotificationController@send');
        
        $router->post("/upload", "FileController@uploadFile");

        $router->post("/groups/create", "GroupController@create");

        $router->post("/types/create", "TypeController@create");

    });
    
    
    $router->get("/me", "AuthController@me");
    $router->post("/logout", "AuthController@logout");
    $router->get("/refresh", "AuthController@refresh");
    $router->post("/refresh", "AuthController@refresh");


});
$router->post("/sendReset", "PasswordResetController@sendResetCode");
$router->post("/register", "AuthController@register");
$router->post("/login", "AuthController@login");
$router->get("/reset", "PasswordResetController@confirmReset");
$router->post("/reset", "PasswordResetController@resetPassword");


// $router->get