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

    $router->post("/notifications/fav", "User_NotificationController@setFavorite");

    $router->post("/users_notifications/read", "User_NotificationController@read");

    $router->post("/users_notifications/download", "User_NotificationController@download");
    
    $router->group(['middleware' => 'admin'], function () use ($router) {

        $router->post("/users/delete", "UserController@delete");

        $router->post("/types/delete", "TypeController@delete");

        $router->post("/groups/delete", "GroupController@delete");

        $router->post("/users/toggleActive", "UserController@toggleActive");

        $router->post("/types/toggleActive", "TypeController@toggleActive");

        $router->post("/groups/toggleActive", "GroupController@toggleActive");

        $router->post("/notifications/toggleActive", "NotificationController@toggleActive");

        $router->post("/users/edit", "UserController@edit");

        $router->post("/types/edit", "TypeController@edit");

        $router->post("/groups/edit", "GroupController@edit");

        $router->get("/users_notifications", "User_NotificationController@getUsersNotificationsByNotification");

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