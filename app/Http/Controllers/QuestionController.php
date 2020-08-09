<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Classes\Logger;
use App\Classes\DirectDebitSender;
use App\Classes\CassException;
use App\question;
use App\marks_store;
use App\Classes\Subscription;
use App\Classes\SubscriptionException;
use App\daily_charging;
use App\premium_charging;
use App\point_table_regular;
use App\challenge_friend_question;
use App\challenge_friend_answer;
use App\exam_count;
$exam_count_paid = 0;
class QuestionController extends Controller {
   
    public $app_id = "APP_005968";
    public $app_password = "186568e5974976fc5ae362d9496a704f";
    public $app_id_subscription_free = 'APP_028448';
    public $app_password_subscription_free = '211a008cdceca5968c742f793843b26f';
    //public $date = date('d-m-Y');
    function random_strings($length_of_string) {
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        // Shufle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($str_result), 0, $length_of_string);
    }
    
    function get_subject_name($subject_id)
    {
        $subject_name = DB::table('tbl_subject')->where('id','=',$subject_id)->first()->subject_name;
        return $subject_name;
    }
    public function overall_stat($user_id) {
         $subjects = DB::table('tbl_subject')->where('status','=',1)->get();
         
         $response = array();
         
         for($i=0;$i<sizeof($subjects);$i++)
         {
             
   
           $sql = "SELECT subject_id,(SUM(answer_verdict)*100/COUNT(id)) as percentage from (SELECT id,subject_id,t1.answer_verdict from ques 
           LEFT JOIN (select substring_index(substring_index(question_id, ',', n), ',', -1) as question_id,substring_index(substring_index(answer_verdict, ',', n), ',', -1)
           as answer_verdict from user_question_track join (SELECT @row := @row + 1 as n FROM 
           (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) t,
           (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) tt,
           (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) ttt,
           (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) tttt,
           (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) ttttt,
           (SELECT @row:=0) r) as
           numbers on char_length(question_id) - char_length(replace(question_id, ',', '')) >= n - 1 WHERE user_id = ".$user_id.") AS
           t1 ON ques.id = t1.question_id where t1.question_id IS NOT NULL AND ques.subject_id = ".$subjects[$i]->id.") as t4";
           
            $result = DB::select(DB::raw($sql));
            if($result[0]->subject_id ==NULL)
            {
              $subject_name = $this->get_subject_name($subjects[$i]->id);
            array_push($response,['subject_name'=>$subject_name,'percentage'=>0]);
                
            }
            else
            {
                $subject_name = $this->get_subject_name($result[0]->subject_id);
            array_push($response,['subject_name'=>$subject_name,'percentage'=>floor($result[0]->percentage)]);
            }
               
            
            //file_put_contents('test.txt',json_encode($result));
       
         }
         $subjects = json_decode(json_encode($response));
         
          //file_put_contents('test.txt',json_encode($response));
        return view('overall_stat',['subjects'=>$subjects]);
    }
    public function get_all_stat(Request $request) {
        $user_id = $request->user_id;
        $subject_id = $request->subject_id;
        $all_exam = marks_store::where('user_id', '=', $user_id)->where('subject_id', '=', $subject_id)->groupBy('exam_date')->orderBy('created_at', 'DESC')->get();
        $response = array();
        for ($i = 0;$i < sizeof($all_exam);$i++) {
            $date = $all_exam[$i]->exam_date;
            $individual_exam = marks_store::where('user_id', '=', $user_id)->where('subject_id', '=', $subject_id)->where('exam_date', '=', $date)->get();
            $question_answer = array();
            $total_question = 0;
            $total_right_answer = 0;
            for ($j = 0;$j < sizeof($individual_exam);$j++) {
                $question_list = json_decode($individual_exam[$j]->question_answer);
                $total_question = $total_question + sizeof($question_list);
                //file_put_contents('test.txt',json_encode($question_list[0]));
                
                for ($k = 0;$k < sizeof($question_list);$k++) {
                    $question_id = $question_list[$k]->question_id;
                    $question = question::where('id', '=', $question_id)->first();
                    $answer_no = question::where('id', '=', $question_id)->first()->correct_answer;
                    $answer_no = 'option' . $answer_no;
                    $answer = $question->$answer_no;
                    // return $answer_no;
                    $question = $question->question;
                    $status = $question_list[$k]->value;
                    if ($status == 1) {
                        $total_right_answer++;
                    }
                    array_push($question_answer, ['question' => $question, 'answer' => $answer, 'status' => $status]);
                }
            }
            // return sizeof($question_list);
            array_push($response, ['date' => $date, 'question' => $question_answer, 'total_right_answer' => $total_right_answer, 'total_question' => $total_question]);
        }
        return response()->json($response);
        //return response()->json(['total_exam'=>10,'Bangla Language and Lirerature'=>40,'Bangladesh Affairs'=>20,'General Science'=>20,'Computer and Information Technology'=>10,'International Affairs'=>10]);
        
    }
    public function delete_challenge(Request $request) {
        $code = $request->code;
        $code = strtoupper($code);
        if (challenge_friend_question::where('code', '=', $code)->update(['challenge_status' => 1])) return response()->json(['status_code' => 200]);
        else return response()->json(['status_code' => 400]);
    }
    public function get_accepted_challenge(Request $request) {
        $user_id = $request->user_id;
        $user_challenge = challenge_friend_question::where('challenge_accepted_id', 'LIKE', "%" . $user_id . "%")->where('challenge_status', '=', 0)->get();
        $friend_record = array();
        $final_array = array();
        if (sizeof($user_challenge) > 0) {
            for ($i = 0;$i < sizeof($user_challenge);$i++) {
                $user_id = $user_challenge[$i]->user_id;
                $name = User::where('id', '=', $user_id)->first()->name;
                $code = $user_challenge[$i]->code;
                $accepted_challenge = challenge_friend_answer::where('code', '=', $code)->get();
                $date = $user_challenge[$i]->created_at;
                $date = explode(' ', $date);
                $date = date("m-d-Y", strtotime($date[0]));;
                $total_accepted_challenge = sizeof($accepted_challenge);
                if ($total_accepted_challenge > 0) {
                    for ($j = 0;$j < sizeof($accepted_challenge);$j++) {
                        $friend_user_id = $accepted_challenge[$j]->user_id;
                        $friend_name = User::where('id', '=', $friend_user_id)->first()->name;
                        $score = $accepted_challenge[$j]->score;
                        array_push($friend_record, ['name' => $friend_name, 'score' => $score, 'user_id' => $friend_user_id]);
                    }
                } else {
                    $friend_record = array();
                }
                array_push($final_array, ['code' => $code, 'challenge_created_by' => $name, 'total_accepted_challenge' => $total_accepted_challenge, 'challenge_details' => $friend_record, 'date' => $date]);
            }
            return response()->json(['status_code' => 200, 'my_challenge' => $final_array]);
        } else {
            return response()->json(['status_code' => 505]);
        }
    }
    public function get_my_challenge(Request $request) {
        $user_id = $request->user_id;
        $user_challenge = challenge_friend_question::where('user_id', '=', $user_id)->where('challenge_status', '=', 0)->get();
        $friend_record = array();
        $final_array = array();
        if (sizeof($user_challenge) > 0) {
            for ($i = 0;$i < sizeof($user_challenge);$i++) {
                $code = $user_challenge[$i]->code;
                $accepted_challenge = challenge_friend_answer::where('code', '=', $code)->get();
                $date = $user_challenge[$i]->created_at;
                $date = explode(' ', $date);
                $date = date("m-d-Y", strtotime($date[0]));;
                $total_accepted_challenge = sizeof($accepted_challenge);
                if ($total_accepted_challenge > 0) {
                    for ($j = 0;$j < sizeof($accepted_challenge);$j++) {
                        $friend_user_id = $accepted_challenge[$j]->user_id;
                        $friend_name = User::where('id', '=', $friend_user_id)->first()->name;
                        $score = $accepted_challenge[$j]->score;
                        array_push($friend_record, ['name' => $friend_name, 'score' => $score, 'user_id' => $friend_user_id]);
                    }
                } else {
                    $friend_record = array();
                }
                array_push($final_array, ['code' => $code, 'total_accepted_challenge' => $total_accepted_challenge, 'challenge_details' => $friend_record, 'date' => $date]);
            }
            return response()->json(['status_code' => 200, 'my_challenge' => $final_array]);
        } else {
            return response()->json(['status_code' => 505]);
        }
    }
    public function submit_answer_challenge_question(Request $request) {
        // file_put_contents('test.txt',$request);
        date_default_timezone_set('Asia/Dhaka');
        $date = date('d-m-Y');
        $code = $request->code;
        $user_id = $request->user_id;
        $score = $request->score;
        $question_answer = json_encode($request->answers);
        challenge_friend_answer::create(['code' => $code, 'user_id' => $user_id, 'score' => $score, 'challenge_date' => $date, 'question_answer' => $question_answer]);
        return response()->json(['status_code' => 200]);
    }
    public function get_challenge_question(Request $request) {
        $code = $request->code;
        $user_id = $request->user_id;
        $question_category = challenge_friend_question::where('code', '=', $code)->where('challenge_status', '=', 0)->first();
        $question = array();
        $question_id = array();
        if ($question_category) {
            $challenge_accepted_id = challenge_friend_question::where('code', '=', $code)->where('challenge_accepted_id', 'LIKE', '%' . $user_id . '%')->where('challenge_status', '=', 0)->first();
            $previous_accepted_id = $question_category->challenge_accepted_id;
            $availability = 0;
            if ($challenge_accepted_id) {
                $availability = 1;
                $challene_accepted_user_id = $challenge_accepted_id->challenge_accepted_id . ',' . $user_id;
                return response()->json(['status_code' => 510]);
            }
            if ($question_category->question_id) {
                $question_id = json_decode($question_category->question_id);
                //return $question_id[0];
                for ($i = 0;$i < sizeof($question_id);$i++) {
                    $ques = question::where('id', '=', $question_id[$i])->first();
                    array_push($question, $ques);
                }
                //  if($availability == 1)
                //  {
                //   challenge_friend_question::where('code','=',$code)->update(['challenge_accepted_id'=>$challenge_accepted_id]);
                //  }
                challenge_friend_question::where('code', '=', $code)->where('challenge_status', '=', 0)->update(['challenge_accepted_id' => $previous_accepted_id . ',' . $user_id]);
                return response()->json(['status_code' => 200, 'question' => $question]);
            } else {
                $category = json_decode($question_category->question_category);
                for ($i = 0;$i < sizeof($category);$i++) {
                    $subject_id = $category[$i]->subject_id;
                    $value = $category[$i]->value;
                    $ques = question::where('subject_id', '=', $subject_id)->inRandomOrder()->limit(2)->get();
                    for ($j = 0;$j < sizeof($ques);$j++) {
                        array_push($question, $ques[$j]);
                        array_push($question_id, $ques[$j]->id);
                    }
                }
                if ($availability == 1) {
                    challenge_friend_question::where('code', '=', $code)->where('challenge_status', '=', 0)->update(['question_id' => json_encode($question_id), 'challenge_accepted_id' => $challenge_accepted_id]);
                } else {
                    challenge_friend_question::where('code', '=', $code)->where('challenge_status', '=', 0)->update(['question_id' => json_encode($question_id), 'challenge_accepted_id' => $user_id]);
                }
                return response()->json(['status_code' => 200, 'question' => $question]);
            }
        } else {
            return response()->json(['status_code' => 503]);
        }
    }
    public function get_code(Request $request) { // file_put_contents('code.txt',$request);
        $question_category = json_encode($request->question_category);
        $user_id = $request->user_id;
        $code = $this->random_strings(6);
        challenge_friend_question::create(['question_category' => $question_category, 'user_id' => $user_id, 'code' => $code]);
        return response()->json(['status_code' => 200, 'code' => $code]);
    }
    public function submit_answer(Request $request) {
        //file_put_contents('test.txt', $request);
        date_default_timezone_set('Asia/Dhaka');
        $date = date('d-m-Y');
        //$req = json_decode(file_get_cont$ents('submit_text.txt'));
        $question_answer = json_encode($request->answers);
        $score = $request->score;
        $user_id = $request->user_id;
        $subject_id =  $request->subject_id;
        $question_id = "";
        $answer_verdict = "";
        $a = $request->answers;
        for ($i = 0;$i < sizeof($a);$i++) {
            $question_id = $a[$i]['question_id'] . "," . $question_id;
            $answer_verdict = $a[$i]['value'] . "," . $answer_verdict;
        }
        $question_id = substr($question_id, 0, -1); //remove last comma
        $answer_verdict = substr($answer_verdict, 0, -1);
        marks_store::create(['user_id' => $user_id, 'score' => $score, 'question_answer' => $question_answer, 'exam_date' => $date, 'subject_id' => $subject_id, 'question_id' => $question_id, 'answer_verdict' => $answer_verdict]);
        //file_put_contents('submit_text.txt',$request);
        $exam_count = exam_count::where('exam_date','=',$date)->where('user_id','=',$user_id)->first();
        if($exam_count)
        {
           $remaining_exam = $exam_count->exam_count + 1;
           exam_count::where('user_id','=',$user_id)->where('exam_date','=',$date)->update(['exam_count'=>$remaining_exam]);
           
        }
        else
        {
           exam_count::create(['exam_count'=>1,'user_id'=>$user_id,'exam_date'=>$date]);
        }
        return response()->json(['status_code' => 200]);
        //
        
    }
    public function admin_question() {
        ///$user_id = Session::get('user_id');
        $fetch = question::get();
        $with_tag = question::where('tag', '!=', NULL)->get();
        $total_with_tag = sizeof($with_tag);
        $total_question = sizeof($fetch);
        $data = '<table id="data-table" class="table">
                    <thead>
                        <tr>
                            <th>#Id</th>
                            <th>Question</th>
                            <th>Option1</th>
                            <th>Option2</th>
                            <th>Option3</th>
                            <th>Option4</th>
                            <th>Correct Option</th>
                            <th>Tag</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>';
        $j = 1;
        for ($i = 0;$i < sizeof($fetch);$i++) {
            $data.= '<tr>
                            <td>' . $j++ . '</td>
                            <td>' . $fetch[$i]->question . '</td>
                            <td>' . $fetch[$i]->option1 . '</td>
                            <td>' . $fetch[$i]->option2 . '</td>
                            <td>' . $fetch[$i]->option3 . '</td>
                            <td>' . $fetch[$i]->option4 . '</td>
                            <td>' . $fetch[$i]->correct_answer . '</td>
                            <td>' . $fetch[$i]->tag . '</td>
                         
                            <td class="text-right">
                               
                                <button onclick="edit_question(' . $fetch[$i]->id . ')" class="btn btn-icon btn-hover btn-sm btn-rounded">
                                    <i class="anticon anticon-edit"></i>
                                </button>
                                <button onclick="delete_question(' . $fetch[$i]->id . ')" class="btn btn-icon btn-hover btn-sm btn-rounded">
                                    <i class="anticon anticon-delete"></i>
                                </button>
                            </td>
                        </tr>';
        }
        $data.= '</tbody>
                </table>
                <script src="' . asset('assets') . '/vendors/datatables/jquery.dataTables.min.js"></script>
                <script src="' . asset('assets') . '/vendors/datatables/dataTables.bootstrap.min.js"></script>
                <script src="' . asset('assets') . '/vendors/datatables/datatables.js"></script>
                <script>
                    $("#data-table").DataTable({
                        paging: false,
                        scrollY: 400
                    });
                </script>
                ';
        $array = array('total_question' => $total_question, "data" => $data, 'with_tag_question' => $total_with_tag);
        //$array = json_decode(json_encode($array));
        return json_encode($array);
    }
    public function edit($id) {
        return json_encode(Question::where('id', $id)->first());
    }
    public function update(Request $request) {
        Question::where('id', $request->id)->update($request->all());
    }
   
    public function subscription_free(Request $request) {
        $user_id = $request->user_id;
        $mobile = User::where('id', '=', $user_id)->first()->mobile;
        $mobile = "tel:88" . $mobile;
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id_subscription_free, $this->app_password_subscription_free);
        try {
          $status =   $subscription->subscribe($mobile);
            //return response()->json(['status_code' => 200]);
        }
        catch(Exception $e) {
           // file_put_contents('test.txt', $e);
           // return response()->json(['status_code' => 400]);
        }
    }
    public function caas_charge_mobile_number(Request $request) {
        
         date_default_timezone_set('Asia/Dhaka');
        $date = date('d-m-Y');
        $mobile_number = $request->msisdn;
        $user_id = $request->user_id;
        $amount = 0.1;
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id_subscription_free, $this->app_password_subscription_free);
        try {
            $x = $subscription->subscribe("tel:88" . $mobile_number);
        }
        catch(exception $e) {
            //file_put_contents("error.txt", $e);
        }
        try {
            $caas = new DirectDebitSender();
            $cass_status = json_decode($caas->cass($user_id, "tel:88" . $mobile_number, $amount));
            //return $cass_status->statusCode;
            if ($cass_status->statusCode === "S1000") {
                user::where('id','=',$user_id)->update(['mobile'=>$mobile_number]);
                daily_charging::create(['statusCode' => $cass_status->statusCode, 'timeStamp' => $cass_status->timeStamp, 'externalTrxId' => $cass_status->externalTrxId, 'statusDetail' => $cass_status->statusDetail, 'internalTrxId' => $cass_status->internalTrxId]);
                   
           exam_count::where('user_id','=',$user_id)->where('exam_date','=',$date)->update(['exam_count'=>0]);
                
                
                return response()->json(['status_code' => 200]);
            } else {
                return response()->json(['status_code' => 511]);
            }
        }
        catch(exception $e) {
            //return $e->getStatusCode;
            
        }
    }
    public function caas_charge_regular(Request $request) {
 date_default_timezone_set('Asia/Dhaka');
        $date = date('d-m-Y');
        $user_id = $request->user_id;
        $amount = 0.1;
        $mobile_number = User::where('id', '=', $user_id)->first()->mobile;
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id_subscription_free, $this->app_password_subscription_free);
        try {
            $x = $subscription->subscribe("tel:88" . $mobile_number);
        }
        catch(exception $e) {
          //  file_put_contents("error.txt", $e);
        }
        try {
            $caas = new DirectDebitSender();
            $cass_status = json_decode($caas->cass($user_id, "tel:88" . $mobile_number, $amount));
            //return $cass_status->statusCode;
            return $cass_status->statusCode;
            if ($cass_status->statusCode === "S1000") {
                daily_charging::create(['statusCode' => $cass_status->statusCode, 'timeStamp' => $cass_status->timeStamp, 'externalTrxId' => $cass_status->externalTrxId, 'statusDetail' => $cass_status->statusDetail, 'internalTrxId' => $cass_status->internalTrxId]);
                // $exam_count = exam_count::where('exam_date','=',$date)->where('user_id','=',$user_id)->first();
             //$remaining_exam = $exam_count->exam_count + 1;
              exam_count::where('user_id','=',$user_id)->where('exam_date','=',$date)->update(['exam_count'=>0]);
                return response()->json(['status_code' => 200]);
            } else {
                return response()->json(['status_code' => 511]);
            }
        }
        catch(exception $e) {
            //return $e->getStatusCode;
            
        }
    }

    public function get_subject(Request $request) {
        $subject_list = DB::table('tbl_subject')->get();
        return response()->json(['status_code' => 200, 'subjects' => $subject_list]);
    }
    
      public function get_subject_challenge_friend(Request $request) {
        $subject_list = DB::table('tbl_subject')->where('status','=',1)->get();
        return response()->json(['status_code' => 200, 'subjects' => $subject_list]);
    }
    function subscription_status_piad($mobile) {
        $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id, $this->app_password);
        $subscription_status = $subscription->getStatus('tel:88' . $mobile);
        
        if ($subscription_status === "REGISTERED") {
            return true;
        } else {
            return false;
        }
    }
    
    function subscription_status_free($mobile)
    {
        
                $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id_subscription_free, $this->app_password_subscription_free);
                $subscription_status = $subscription->getStatus('tel:88' . $mobile);
                return $subscription_status;
    }
    
   
    public function check_availability(Request $request)
    {
        $user_id = $request->user_id;
        $mobile = User::where('id', '=', $user_id)->first()->mobile;
         date_default_timezone_set('Asia/Dhaka');
        $date = date('d-m-Y');
        $exam = exam_count::where('user_id', '=', $user_id)->where('exam_date', '=', $date)->first();
        if($exam)
        {
            $exam_count = $exam->exam_count;
            $daily_subscription_limit = $exam->daily_subscription_exam;
            if($exam_count == 9)
            {
                exam_count::where('user_id', '=', $user_id)->where('exam_date', '=', $date)->update(['daily_subscription_exam'=>1]);
            }
        }
        else
        {
            $exam_count = 0;
        }
        
       // $exam_count = 6;
        
        // if($exam_count_paid !=0)
        // {
        //     $exam_count = 0;
        //     $this->exam_count_paid--;
        // }
        
        
        
        
       // return $exam_count;
        
        if ($exam_count>=5) {
            if ($mobile) {
                if ($this->subscription_status_piad($mobile)) {
                    if($daily_subscription_limit == 0){
                    $availability = true;
                    }
                    
                    else
                    {
                         $availability = false;
                    }
                    
                    
                } else {
                    $availability = false;
                }
            }
            else
            {
                 $availability = false;
            }
            
        } else {
            $availability = true;
        }
        
        
        $availability = 'false';
        if($mobile)
        {
            $sub_status = $this->subscription_status_piad($mobile);
            
            return response()->json(['msisdn'=>true,'availability'=>$availability,'sub_status'=>$sub_status,]);
        }
        else
        {
           if($availability)
           {
           return response()->json(['msisdn'=>false,'availability'=>true]); 
           }
           else
           {
           return response()->json(['msisdn'=>false,'availability'=>false]);
           }
           
        }
        
        
         
        
    }
  
    public function question_for_regular_exam(Request $request) {
        date_default_timezone_set('Asia/Dhaka');
        $date = date('d-m-Y');
        $subject_id = $request->subject_id;
        $user_id = $request->user_id;
        $question_amount = 10;
       if($subject_id == 6)
       {
           $sql = "SELECT * from (SELECT * from ques LEFT JOIN (select substring_index(substring_index(question_id, ',', n), ',', -1) as question_id from user_question_track join 
        (SELECT @row := @row + 1 as n FROM 
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) t,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) tt,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) ttt,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) tttt,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) ttttt,
        (SELECT @row:=0) r) as numbers on char_length(question_id) - char_length(replace(question_id, ',', '')) >= n - 1 WHERE user_id = " . $user_id . ") AS t1 ON ques.id = t1.question_id
        where t1.question_id IS NULL ) as t2  ORDER BY RAND() LIMIT ".$question_amount." ";
       }
       else
       {
        $sql = "SELECT * from (SELECT * from ques LEFT JOIN (select substring_index(substring_index(question_id, ',', n), ',', -1) as question_id from user_question_track join 
        (SELECT @row := @row + 1 as n FROM 
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) t,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) tt,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) ttt,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) tttt,
        (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) ttttt,
        (SELECT @row:=0) r) as numbers on char_length(question_id) - char_length(replace(question_id, ',', '')) >= n - 1 WHERE user_id = " . $user_id . ") AS t1 ON ques.id = t1.question_id
        where t1.question_id IS NULL ) as t2 WHERE t2.subject_id =" . $subject_id . "  ORDER BY RAND() LIMIT ".$question_amount." ";
       }
        $question = DB::select(DB::raw($sql));
        return response(['question'=>$question]);
       
        
    }
    public function check_subscription(Request $request) {
        $user_id = $request->user_id;
        $mobile = User::where('id', '=', $user_id)->first()->mobile;
        try {
            $subscription = new Subscription('https://developer.bdapps.com/subscription/send', $this->app_id_subscription_free, $this->app_password_subscription_free);
            $subscription_status = $subscription->getStatus('tel:88' . $mobile);
            return response()->json(['subscription_status' => $subscription_status, 'status_code' => 200]);
        }
        catch(Exception $e) {
            return response()->json(['status_code' => 400]);
        }
    }
  
}
