<?php

namespace App\Classes;

use App\Classes\Core;

class OtpSender extends core{
    var $server = "https://developer.bdapps.com/subscription/otp/request";
    var $applicationId;
    var $password;
    var $applicationHash = "Eexam";
    var $client = "MOBILEAPP";
    var $device = "Samsung";
    var $os = "Android";
    var $appcode = "https://play.google.com/store/apps/details?id=lk.dialog.megarunlor";//"https://play.google.com/store/apps/details?id=lk.dialog.megarunlor    
    

			

    public function __construct($applicationId,$password){
        
        $this->applicationId = $applicationId;
        $this->password = $password;
      
    }
    public function send_otp($address)
    {
        $arrayField = array(
            'applicationId'=>$this->applicationId,
            'password'=>$this->password,
            'subscriberId'=>"tel:88".$address,
            'applicationHash'=>$this->applicationHash,
            'applicationMetaData'=>
            array(
                'client'=>$this->client,
                'device'=>$this->device,
                'os'=>$this->os,
                'appCode'=>$this->appcode
            )
            );
            $jsonObjectFields = json_encode($arrayField);
            //return json_decode()
            return json_decode($this->handleResponse(json_decode($this->sendRequest($jsonObjectFields,$this->server))));

    }
    
 
	
	private function handleResponse($jsonResponse){
	    //file_put_contents('test2.txt',$jsonResponse); 
    
        if(empty($jsonResponse))
            throw new CassException('Invalid server URL', '500');
        
        $statusCode = $jsonResponse->statusCode;
        if($statusCode === "S1000")
        {
            $statusDetail = $jsonResponse->statusDetail;
            $referenceNo = $jsonResponse->referenceNo;
            return json_encode(['statusCode'=>$statusCode,'statusDetail'=>$statusDetail,'referenceNo'=>$referenceNo]);
        }
        else
        {
            $statusDetail = $jsonResponse->statusDetail;
         
            return json_encode(['statusCode'=>$statusCode,'statusDetail'=>$statusDetail]);
        }
       

        //$requestId = $jsonResponse->requestId;
        // $subscriptionStatus = $jsonResponse->requestId;
        
        // if(!(empty($jsonResponse->subscriptionStatus)))
        //     return $jsonResponse->statusCode;
        
        // if(strcmp($statusCode, 'S1000')==0)
        //     return $statusCode;
        // else
        //     throw new CassException($statusDetail, $statusCode);
    }
}