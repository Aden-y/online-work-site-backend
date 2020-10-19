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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', 'RegisterController@register');
Route::post('/register-client', 'RegisterController@register_client');
Route::get('/skills', 'SkillsController@get');
Route::post('/login', 'AuthController@login');

Route::group([
    'middleware' => ['api', 'auth'],
], function ($router) {


    Route::post('/jobs/submit', 'JobsController@upload_submission');
    Route::post('/jobs/post', 'JobsController@post');
    Route::post('/jobs/assign', 'JobsController@assign');
    Route::post('/messages/send', 'MessagesController@send');
    Route::post('/jobs/rating', 'JobsController@update_rating');


    Route::get('/messages/order-messages/{id}', 'MessagesController@order_messages');
    Route::get('/jobs/index', 'JobsController@index');
    Route::get('/jobs/find', 'JobsController@find_jobs');
    Route::get('/jobs/bidded', 'JobsController@get_freelancer_jobs_bidded');
    Route::get('/jobs/in-bidding', 'JobsController@get_client_jobs_in_bidding');
    Route::get('/jobs/client_jobs', 'JobsController@client_jobs');
    Route::post('jobs/message/{id}', 'JobsController@message');
    Route::post('jobs/bid/{id}', 'JobsController@bid');
    Route::get('jobs/details/{id}', 'JobsController@job');
    Route::get('jobs/bids/{id}', 'JobsController@get_bids');
    Route::get('/jobs/client-inprogress', 'JobsController@get_client_jobs_in_progress');
    Route::get('/jobs/client-cancelled', 'JobsController@get_client_jobs_cancelled');
    Route::get('/jobs/client-completed', 'JobsController@get_client_jobs_completed');

    Route::get('/jobs/freelancer-inprogress', 'JobsController@get_freelancer_jobs_inprogress');
    Route::get('/jobs/freelancer-cancelled', 'JobsController@get_freelancer_jobs_cancelled');
    Route::get('/jobs/freelancer-completed', 'JobsController@get_freelancer_jobs_completed');
    Route::get('/jobs/submissions/{id}', 'JobsController@get_submissions');
    Route::get('/jobs/submissions/download/{id}', 'JobsController@download_submissions');
    Route::get('/jobs/order-files/download/{id}', 'JobsController@download_job_file');
    Route::get('/skills/all', 'SkillsController@index');
});
