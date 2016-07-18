<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use Illuminate\Support\Facades\Request;

Route::get('/','RootController@showInbox');

Route::auth();

Route::get('/logout',function(){
    Auth::logout();
    return view('auth.login');
});

Route::get('/inbox','MailInboxController@showInbox');
Route::get('/profile','ProfileController@showProfile');

Route::get('/xml',function(){
    return view('xml');
});
Route::get('/server/{get}','ServerController@getAjax');
Route::post('/server','ServerController@postAjax');

Route::get('/Users','UsersController@showUsers');
Route::get('/notification','NotificationController@showNotif');
Route::get('/compose','ComposeController@showCompose');
Route::get('/readEmail/{messages_id}',function($messages_id){
    return View::make('readEmail',['messages_id'=>$messages_id]);
});
Route::get('/attachment/{filename}',function($filename){
    $file='./attachment/'. $filename;

    $headers = array(
        'Content-Type: application/pdf',
    );

    return response()->download($filename);
    //return View::make('readEmail',['messages_id'=>$filename]);
});

//////////////////////
Route::get('/posttest',function(){
    return View::make('posttest');
});