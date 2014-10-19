<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
/*
Route::get('/', function()
{
	return View::make('hello');
});
*/

//FILEUPLOAD 
Route::get('/', 'EmailController@start');
Route::get('email/', 'EmailController@loadForm');
Route::post('email/', 'EmailController@process');
Route::get('email/{composerId}', 'EmailController@process');
Route::post('email/{composerId}', 'EmailController@process');
Route::delete('email/{composerId}', 'EmailController@process');
