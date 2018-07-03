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


Route::post('auth/login', 'Auth\ApiAuthController@login');
Route::post('auth/refresh', 'Auth\ApiAuthController@refreshToken');
Route::post('auth/register', 'Auth\ApiAuthController@register');
Route::post('auth/forgot/password','Auth\ForgotPasswordController@getResetToken');
Route::post('auth/reset/password','Auth\ResetPasswordController@reset');

Route::post('/user/verify','Auth\ApiAuthController@verifyUser');

Route::group(['middleware' => ['auth:api']], function () {

    Route::get('/user', 'UserController@getUser');

    // Route::get('/test', 'EventController@showReport');
    Route::get('/event/{id}/attending/report/','EventController@downloadAttendingReport');

    Route::get('/profile', 'UserController@getProfile');
    Route::post('/profile', 'UserController@updateProfile');

    Route::post('/first/login/student', 'UserController@firstLoginStudent');
    Route::post('/first/login/faculty', 'UserController@firstLoginFaculty');

    Route::get('/events/registered' , 'UserController@registeredEvents');
    Route::get('/events/queued','UserController@queuedEvents');
    Route::post('event/{id}/register','UserController@registerEvent');
    Route::post('/event/{id}/queue','UserController@queueUser');

    Route::get('/events', 'EventController@index');
    Route::get('/event/{id}','EventController@show');
    Route::get('/event/{id}/registered','EventController@usersRegisteredForEvent');
    Route::get('/event/{id}/queued','EventController@usersQueuedForEvent');
    Route::get('/event/{id}/attendance','EventController@getAttendance');
    Route::post('/drop/registration/{id}','EventController@dropRegistration');
    
    // Route::post('/event/message','EventController@massMessage');

    Route::post('/mark/attendance/{id}','EventController@markAttendance');
    Route::put('/mark/attendance/{id}','EventController@updateAttendance');

    Route::put('/event/{approval_id}','EventController@update');   
    Route::post('/event', 'EventController@requestApproval');           //faculty creates an event

    Route::get('/my/approval','EventController@toApproveList');         //approval list provided to HOD , program=Cord. etc.
    Route::get('/my/events','EventController@facultyEvents'); //faculties[eventCord.] events that are pending approval 
    Route::post('/event/approve','EventController@approve');
    Route::post('/event/deny','EventController@deniedApproval');

    Route::get('/admin/users','AdminController@getAllUsers');
    Route::get('/admin/roles/suggestions','AdminController@getSuggestions');
    Route::post('/admin/user/role','AdminController@updateUserRole');


    // Route::get('test/{file}','AdminController@testImage');
    // Route::post('test','AdminController@test');
});




// Route::middleware('auth:api')->get('logout','Auth\LoginController@logout');