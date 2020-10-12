<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use App\daily_charging;
use App\premium_charging;
use App\point_table_regular;
use App\point_table_premium;
use App\withdraw;
use App\Classes\Logger;
use App\Classes\Subscription;
use App\Classes\SubscriptionException;
use App\Classes\UssdReceiver;
use App\Classes\UssdSender;
use App\Classes\UssdException;
use App\Classes\SMSSender;
use App\Classes\SMSReceiver;
use App\Classes\SMSServiceException;
use App\Classes\SubscriptionReceiver;
use App\ussd_user;
use App\otp_check;
use App\subscription_status;
use App\Classes\OtpSender;
class UserController extends Controller {
    //
    public $successStatus = 200;
    public $app_id = "APP_005968";
    public $app_password = "186568e5974976fc5ae362d9496a704f";
    public $app_id_subscription_free = 'APP_028448';
    public $app_password_subscription_free = '211a008cdceca5968c742f793843b26f';
    public function login() {
        if (Auth::attempt(['mobile' => request('mobile'), 'password' => request('password') ])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('quiz')->accessToken;
            $success['id'] = $user->id;
            return response()->json(['error' => 'no', 'user' => $success]);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function send_bdapps_otp(Request $request)
    {
        $address = $request->mobile;
        $otp_sender = new OtpSender($this->app_id,$this->app_password);
        $a = $otp_sender->send_otp($address);
        $status_code = $a->statusCode;
        $reference_no = $a->referenceNo;
        return $reference_no;
        //return json_encode($a);
    }
    public function test()
    {
        
    }
    public function check_user()
    {
        $a = User::get();
        return sizeof($a);
    }

    public function check_ussd_user()
    {
        $a = ussd_user::get();
        $total_dialed = sizeof($a);
        $total_subscriber = sizeof(subscription_status::where('medium','=','ussd')->where('status','=',"REGISTERED")->get());
        return "Total try = ".$total_dialed." "."total_subscriber = ".$total_subscriber;
    }
    public function feedback(Request $request)
    {
        //file_put_contents('test_feedback.txt',$requesty);
        return response()->json(['status_code'=>200]);
    }
    public function get_user_id_by_email(Request $request) {
        $email = $request->email;
        $user = User::where('email', '=', $email)->first();
        $success['token'] = $user->createToken('quiz')->accessToken;
        $success['id'] = $user->id;
        $success['status_code'] = 200;
        return response()->json($success);
    }
    public function get_details_by_user_id(Request $request) {
        $user_id = $request->user_id;
        $user = User::where('id', '=', $user_id)->first();
        return response()->json($user);
    }
    public function edit_profile(Request $request) {
        $user_id = $request->user_id;
        if ($request->name) {
            User::where('id', '=', $user_id)->update(['name' => $request->name]);
        }
        if ($request->email) {
            User::where('id', '=', $user_id)->update(['email' => $request->email]);
        }
        if ($request->msisdn) {
            User::where('id', '=', $user_id)->update(['mobile' => $request->msisdn]);
        }
        if ($request->city) {
            User::where('id', '=', $user_id)->update(['city' => $request->city]);
        }
        if ($request->current_study) {
            User::where('id', '=', $user_id)->update(['current_study' => $request->current_study]);
        }
        return response()->json(['status_code' => 200]);
    }
    public function check_subscription(Request $request) {
        $user_id = $request->user_id;
        $user_mobile = User::where('id', '=', $user_id)->first()->mobile;
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id, $this->app_password);
        $status = "UNREGISTERED";
        try {
            $status = $subscription->getStatus('tel:88' . $user_mobile);
        }
        catch(Exception $e) {
        }
        return response()->json(['response' => $status]);
    }
    
    public function subscription_notification(Request $request)
    {
    
    $sender = new SMSSender("https://developer.bdapps.com/sms/send", $this->app_id,$this->app_password);
    
    $receiver 	= new SubscriptionReceiver();
   // file_put_contents('ttest_subscription.txt','Hello');
    // file_put_contents('ttt.txt',"hello");s
    // $frequency = $receiver->getFrequency();
     $status = $receiver->getStatus();
    
      $application_id = $receiver->getApplicationId();
     $address = $receiver->getsubscriberId();
     $address = ltrim($address, '88'); 
     $timestamp = $receiver->getTimestamp();

     $myfile = fopen("SubscriptionNotificationLog.txt", "a+") or die("Unable to open file!");
     fwrite($myfile,$application_id." ".$address." ".$timestamp." ".$timestamp." "."Eexam"."\n");
     
    if(user::where('mobile','=',$address)->first())
    {
        user::where('mobile','=',$address)->update(['subscription_status'=>$status]);
    }
    if(subscription_status::where('mobile','=',$address)->first())
    {
        subscription_status::where('mobile','=',$address)->update(['status'=>$status]);
    }
    else 
    {
        subscription_status::create(['status'=>$status,'mobile'=>$address,'timestamp'=>$timestamp]);
    }

    if(ussd_user::where('user_mobile','=',$address)->first())
    {
        subscription_status::where('mobile','=',$address)->update(['medium'=>'ussd']);
    }
     //user::where('mobile','=',"tel:".$address)->update(['status'=>$status]);
    
    
    $sender->sms("Download the app. https://play.google.com/store/apps/details?id=xit.zubrein.eexam","tel:88".$address);
    }
    public function subscription_free(Request $request) {
        $user_id = $request->user_id;
        $user_mobile = User::where('id', '=', $user_id)->first()->mobile;
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id_subscription_free, $this->app_password_subscription_free);
        try {
            $status_code = $subscription->subscribe('tel:88' . $user_mobile);
            //return response()->json(['status_code' => 200]);
        }
        catch(Exception $e) {
            
            //return response()->json(['status_code' => 400]);
        }
        if($status_code === 'S1000')
        {
            return response()->json(['status_code' => 200]);
        }
        else
        {
            return response()->json(['status_code' => 400]);
        }
    }

    public function subb()
    {
        $ussd_user = ussd_user::get();
        for($i=0;$i<sizeof($ussd_user);$i++)
        {
        $mobile = "tel:88" . $ussd_user[$i]->user_mobile;
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id, $this->app_password);
        try {
            $status_code = $subscription->subscribe($mobile);
           // return $status_code;
            
            //return response ()->json(['status_code' => 200]);
        }
        catch(Exception $e) {
           // file_put_contents('test.txt', $e);
            //return response()->json(['status_code' => 400]);
        }
        if($status_code === 'S1000')
        {
        //     $myfile = fopen("subscription log.txt", "a+") or die("Unable to open file!");
        //      fwrite($myfile,$status_code." ".$mobile."\n");

            echo response ()->json(['status_code' => 200]);
        }
        
        else
        {
             echo response ()->json(['status_code' => 400]);
        }
    }
    
    }

    public function send_sms_all_user()
    {
        //$mobile_number = "tel:88" . $request->msisdn;
        $sender = new SMSSender("https://developer.bdapps.com/sms/send", $this->app_id,$this->app_password);
        $msg = "Download the app. https://play.google.com/store/apps/details?id=xit.zubrein.eexam" ;
        
        //$server = 'https://developer.bdapps.com/sms/send';
       // $sender = new SMSSender($server, $this->app_id, $this->app_password);
      //  $otp = mt_rand(1000, 9999);
        //$msg = "Your e-exam otp is ".$otp;
        try {
            $a = $sender->sms($br);
           // file_put_contents('test.txt',$a);
           return response()->json(['response'=>'ok']);
          
        }
        catch(Exception $e) {
            return response()->json([$e]);
        }
    }
     public function subscription_paid(Request $request) {
        //  date_default_timezone_set('Asia/Dhaka');
        // $date = date('d-m-Y');
        
        $user_id = $request->user_id;
        $mobile = User::where('id', '=', $user_id)->first()->mobile;
        $mobile = "tel:88" . $mobile;
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id, $this->app_password);
        try {
            $status_code = $subscription->subscribe($mobile);
           // return $status_code;
            
            //return response ()->json(['status_code' => 200]);
        }
        catch(Exception $e) {
           // file_put_contents('test.txt', $e);
            //return response()->json(['status_code' => 400]);
        }
        if($status_code === 'S1000')
        {
        //     $myfile = fopen("subscription log.txt", "a+") or die("Unable to open file!");
        //      fwrite($myfile,$status_code." ".$mobile."\n");

            return response ()->json(['status_code' => 200]);
        }
        
        else
        {
             return response ()->json(['status_code' => 400]);
        }
    }
    public function otp_login(Request $request) {
        $mobile_number = $request->mobile_number;
        $user = User::where('mobile', '=', $mobile_number)->first();
        if ($user) {
            $success['token'] = $user->createToken('quiz')->accessToken;
            $success['id'] = $user->id;
            return response()->json(['error' => 'no', 'user' => $success]);
        } else {
            return response()->json(['error' => 'number not registered']);
        }
    }
    public function version_code(Request $request) {
        $code = $request->code;
        $version = DB::table('version_control')->where('version_code', '=', $code)->first();
        return response()->json(['active' => $version->active, 'app_link' => 'https://bit.ly/2Ul13wh']);
    }
   

    public function manual_otp_check(Request $request) {
        $mobile = $request->msisdn;
        $otp = $request->otp;
        $otp_check = otp_check::where('msisdn', '=', $mobile)->where('otp', '=', $otp)->first();
        if ($otp_check) {
            return response()->json(['status_code' => 200]);
        } else {
            return response()->json(['status_code' => 401]);
        }
    }
    public function check_otp(Request $request) {
        $mobile = $request->msisdn;
        $otp = $request->otp;
        $otp_check = otp_check::where('msisdn', '=', $mobile)->where('otp', '=', $otp)->first();
        if ($otp_check) {
            $valid = User::where('mobile', '=', $mobile)->first();
            if ($valid) {
                $token = $valid->createToken('quiz')->accessToken;
                $success['id'] = $valid->id;
                $success['status_code'] = 200;
                return response()->json(['status_code' => 200, 'registered' => 'yes', 'token' => $token, 'user_details' => $valid]);
            } else {
                return response()->json(['status_code' => 200, 'registered' => 'no']);
            }
        } else {
            return response()->json(['status_code' => 400]);
        }
    }

    public function send_sms(Request $request) {
        // $mobile = '01845318609';
        $mobile_number = "tel:88" . $request->msisdn;
        $msg = $request->msg;
        
        $server = 'https://developer.bdapps.com/sms/send';
        $sender = new SMSSender($server, $this->app_id, $this->app_password);
      //  $otp = mt_rand(1000, 9999);
        //$msg = "Your e-exam otp is ".$otp;
        try {
            $a = $sender->sms($msg, $mobile_number);
           // file_put_contents('test.txt',$a);
           return response()->json(['response'=>'ok']);
          
        }
        catch(Exception $e) {
            return response()->json([$e]);
        }
    }

    public function send_otp(Request $request) {
        // $mobile = '01845318609';
        $mobile_number = "tel:88" . $request->msisdn;
        
        $server = 'https://developer.bdapps.com/sms/send';
        $sender = new SMSSender($server, $this->app_id, $this->app_password);
        $otp = mt_rand(1000, 9999);
        $msg = "Your e-exam otp is ".$otp;
        try {
            $a = $sender->sms($msg, $mobile_number);
           // file_put_contents('test.txt',$a);
            $user = otp_check::where('msisdn', '=', $request->msisdn)->first();
            if ($user) {
                otp_check::where('msisdn', '=', $request->msisdn)->update(['otp' => $otp]);
            } else {
                otp_check::create(['msisdn' => $request->msisdn, 'otp' => $otp]);
            }
            if($a ==="S1000")
            {
                return response()->json(['status_code' => 200]);
            }
            else {
               

                return response()->json(['status_code' => 514]);
            }
            
        }
        catch(Exception $e) {
            return response()->json([$e]);
        }
    }
        public function send_message(Request $request) {
        $mobile_number = "tel:88" . $request->msisdn;
        $message = $request->message;
        
        $server = 'https://developer.bdapps.com/sms/send';
        $sender = new SMSSender($server, $this->app_id, $this->app_password);
        
        $msg = $message;
        try {
            $a = $sender->sms($msg, $mobile_number);
            
            return response()->json(['status_code' => 200]);
        }
        catch(Exception $e) {
            return response()->json([$e]);
        }
    }
    

    
    function email_validation($email)
    {
       $email =  User::where('email','=',$email)->first();
       if($email)
       {
           return false;
       }
       else
       {
           return true;
       }
    }
    
    public function register(Request $request) {
        if ($request->msisdn) {
            if ($this->email_validation($request->email)) {
                $mobile = $request->msisdn;
                $email = $request->email;
                $name = $request->name;
                $city = $request->city;
                $current_study = $request->current_study;
                User::create(['name' => $name, 'city' => $city, 'current_study' => $current_study, 'email' => $email, 'mobile' => $mobile]);
                $user = User::where('mobile', '=', $mobile)->first();
                $success['token'] = $user->createToken('quiz')->accessToken;
                $success['id'] = $user->id;
                $success['status_code'] = 200;
                return response()->json($success);
            } else {
                return response()->json(['status_code' => 512]);
            }
        } else if ($request->email) {
            if ($this->email_validation($request->email)) {
                $email = $request->email;
                $name = $request->name;
                $city = $request->city;
                $current_study = $request->current_study;
                User::create(['name' => $name, 'city' => $city, 'current_study' => $current_study, 'email' => $email]);
                $user = User::where('email', '=', $email)->first();
                $success['token'] = $user->createToken('quiz')->accessToken;
                $success['id'] = $user->id;
                $success['status_code'] = 200;
                return response()->json($success);
            } else {
                return response()->json(['status_code' => 512]);
            }
        }
    }
    public function details() {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    public function  arif_ussd() {
      //  file_put_contents('arif_vai_test.txt',"hello");
        $production = true;
        if ($production == false) {
            $ussdserverurl = 'http://localhost:7000/ussd/send';
        } else {
            $ussdserverurl = 'https://developer.bdapps.com/ussd/send';
        }
        try {
            $receiver = new UssdReceiver();
            $ussdSender = new UssdSender($ussdserverurl,"APP_004014", "e5e324f7187af22313f295dc6aed269e");
            //$subscription = new Subscription('https://developer.bdapps.com/subscription/send',"APP_004014","e5e324f7187af22313f295dc6aed269e");
            // ile_put_contents('text.txt',$receiver->getRequestID());
            //$operations = new Operations();
            //$receiverSessionId  =   $receiver->getSessionId();
            $content = $receiver->getMessage(); // get the message content
            $address = $receiver->getAddress(); // get the ussdSender's address
            $requestId = $receiver->getRequestID(); // get the request ID
            $applicationId = $receiver->getApplicationId(); // get application ID
            $encoding = $receiver->getEncoding(); // get the encoding value
            $version = $receiver->getVersion(); // get the version
            $sessionId = $receiver->getSessionId(); // get the session ID;
            $ussdOperation = $receiver->getUssdOperation(); // get the ussd operation
            //file_put_contents('status.txt',$address);
            $converted_address = ltrim($address, 'tel:');
            $result = DB::table('arif_vai')->where('number',$converted_address)->first();
            $column_b = $result->columnb;
            $column_c = $result->columnc;
            $column_d = $result->columnd;
            $responseMsg = "Thank you for your query"."\n"."No of adjusted share = ".$column_b."\n"." Adjusted share (in BDT) = ".$column_c."\n"."Total Share Allocated = ".$column_d;
           // $responseMsg = " Thank you for your Subscription";
            if ($ussdOperation == "mo-init") {
                try {
                    $ussdSender->ussd($sessionId, $responseMsg, $address, 'mt-fin');
                    // $converted_address = ltrim($address, 'tel:88'); 
                    // if(ussd_user::where('user_mobile','=',$converted_address)->first())
                    // {
                    
                    // }
                    // else
                    // {
                    //     ussd_user::create(['user_mobile' => $converted_address]);
                    // }
                    // $subscription->subscribe($address);
                }
                catch(Exception $e) {
                }
            }
        }
        catch(Exception $e) {
          //  file_put_contents('USSDERROR.txt', $e);
        }
    }

    public function ussd() {
        //return $a;
        $production = true;
        if ($production == false) {
            $ussdserverurl = 'http://localhost:7000/ussd/send';
        } else {
            $ussdserverurl = 'https://developer.bdapps.com/ussd/send';
        }
        try {
            $receiver = new UssdReceiver();
            $ussdSender = new UssdSender($ussdserverurl, $this->app_id, $this->app_password);
            $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id, $this->app_password);
            // ile_put_contents('text.txt',$receiver->getRequestID());
            //$operations = new Operations();
            //$receiverSessionId  =   $receiver->getSessionId();
            $content = $receiver->getMessage(); // get the message content
            $address = $receiver->getAddress(); // get the ussdSender's address
            $requestId = $receiver->getRequestID(); // get the request ID
            $applicationId = $receiver->getApplicationId(); // get application ID
            $encoding = $receiver->getEncoding(); // get the encoding value
            $version = $receiver->getVersion(); // get the version
            $sessionId = $receiver->getSessionId(); // get the session ID;
            $ussdOperation = $receiver->getUssdOperation(); // get the ussd operation
            //file_put_contents('status.txt',$address);
            $responseMsg = " Thank you for your Subscription.";
            if ($ussdOperation == "mo-init") {
                try {
                    $ussdSender->ussd($sessionId, $responseMsg, $address, 'mt-fin');
                    $converted_address = ltrim($address, 'tel:88'); 
                    if(ussd_user::where('user_mobile','=',$converted_address)->first())
                    {
                    
                    }
                    else
                    {
                        ussd_user::create(['user_mobile' => $converted_address]);
                    }
                    $subscription->subscribe($address);
                }
                catch(Exception $e) {
                }
            }
        }
        catch(Exception $e) {
          //  file_put_contents('USSDERROR.txt', $e);
        }
    }
  
    public function getMsisdn(Request $request) {
        $msisdn = $request->header('msisdn');
        //return $msisdn;
        if (!isset($msisdn)) {
            $msg = "Please use mobile data of Robi or Airtel operator!";
            return ('error');
        } else {
            return compact('msisdn');
        }
    }
}
