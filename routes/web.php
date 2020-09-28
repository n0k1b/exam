<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::get('check_user','UserController@check_user');
Route::get('check_ussd_user','UserController@check_ussd_user');

Route::get('faq',function()
{
   return view('faq');  
}

);

Route::get('test','UserController@test');
Route::get('admin-question',function()
{
   return view('admin_question');  
}

);
Route::get('EditQuestion/{id}','QuestionController@edit');
Route::post('UpdateQuestion','QuestionController@update');

Route::get('show_question','QuestionController@admin_question');


Route::get('prize_pool',function()
{
   return view('prize_pool');  
}

);

Route::post('ussd','UserController@ussd');
Route::post('arif_ussd','UserController@arif_ussd');

Route::get("subb",'UserController@subb');

Route::post('SubscriptionNotificationPaid','UserController@subscription_notification');

Route::get('getMsisdn',"UserController@getMsisdn");