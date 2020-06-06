<?php

use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//just for displaying errors --separated
Route::get('/error/{message}', function(Request $request){
    echo $request->message;
})->name('error_message');

//routes API create_game and "redirect"
Route::get('/create-game', 'GameController@index')->name('create_game');
Route::get('/game/{id}/', 'GameController@noMethod')->name('current_game');

//add_army API route
Route::get('/game/{id}/add-army/', 'GameController@store')->name('store_game');

//API list_games route
Route::get('/games', 'GameController@list');

//API run_attack route
Route::get('/game/{id}/run-attack', 'GameController@run')->name('run_attack');

//API game log routes
Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//API restart_game route
Route::get('/game/{id}/restart-game', 'GameController@update')->name('restart_game');

