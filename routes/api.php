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

Route::post('register', 'UserController@register');
Route::post('send_sms',"UserController@send_sms");
Route::post('send_bdapps_otp','UserController@send_bdapps_otp');
Route::post('login', 'UserController@login');
Route::post('otp_login','UserController@otp_login');

Route::post('send_otp','UserController@send_otp');

Route::post('send_message','UserController@send_message');

Route::post('confirm_otp','UserController@check_otp');


Route::post('test_curl','QuestionController@daily_subscribe');
Route::post('weekly_charge','QuestionController@weekly_charge');
Route::post('monthly_charge','QuestionController@monthly_charging');
Route::post('version_code','UserController@version_code');
Route::post('get_user_id_by_email','UserController@get_user_id_by_email');


 Route::get('overall_stat/{id}','QuestionController@overall_stat');


Route::group(['middleware' => 'auth:api'], function()
{
    Route::post('manual_otp_check', 'UserController@manual_otp_check');
    Route::post('set_withdraw_number', 'UserController@set_withdraw_number');
   Route::post('details', 'UserController@details');
   
  
Route::post('get_details_by_user_id','UserController@get_details_by_user_id');
    
   Route::post('question_for_regular_exam',"QuestionController@question_for_regular_exam");
   
   Route::post('get_live_quiz_news','PointController@get_live_quiz_news');
   
  // Route::post('edit_profile','UserController@edit_profile');
   
   Route::post("get_question",'QuestionController@get_question');
   
   Route::post("submit_answer",'QuestionController@submit_answer');
   
   Route::post("withdraw_request",'PointController@withdraw_request');
   
    Route::post("get_profile",'UserController@get_profile');
    
     Route::post("leaderboard",'PointController@leaderboard');
     
     Route::post("live_leaderboard",'PointController@live_leaderboard');
     
     
     Route::post('get_code','QuestionController@get_code');
     
      Route::post('get_challenge_question','QuestionController@get_challenge_question');
      Route::post('submit_answer_challenge_question','QuestionController@submit_answer_challenge_question');
      
     
     Route::post("live_contest",'PointController@live_contest');
     
     Route::post('live_contest_answer_submit','PointController@live_contest_answer_submit');
     
     
     Route::post('check_subscription','QuestionController@check_subscription');
     
     Route::post('subscription_free','UserController@subscription_free');
       Route::post('subscription_paid','UserController@subscription_paid');
     
     Route::post('cass_charge_regular','QuestionController@caas_charge_regular');
     
     Route::post('subjects','QuestionController@get_subject');
      Route::post('challenge_friend_subjects','QuestionController@get_subject_challenge_friend');
     Route::post('get_my_challenge','QuestionController@get_my_challenge');
     
     Route::post('get_accepted_challenge','QuestionController@get_accepted_challenge');
     
     Route::post('get_all_stat','QuestionController@get_all_stat');
     
     Route::post('delete_challenge','QuestionController@delete_challenge');
      Route::post('feedback','UserController@feedback');
     
        Route::post('edit_profile','UserController@edit_profile');
        
        
         Route::post('caas_charge_mobile_number','QuestionController@caas_charge_mobile_number');
         
           Route::post('check_availability','QuestionController@check_availability');
           
         Route::post('SubscriptionNotificationPaid','UserController@subscription_notification');  
     
       
   
    
   
   
   
   
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
