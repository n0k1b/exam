<?php

namespace App\Classes;

use App\Classes\Core;

class VerifyOtp extends core{
    var $server = "https://developer.bdapps.com/subscription/otp/verify";
    var $applicationId;
    var $password;
    var $referenceNo;
    var $otp;
	

    public function __construct($applicationId,$password){
        
        $this->applicationId = $applicationId;
        $this->password = $password;
      
    }
    public function verify_otp($otp,$referenceNo)
    {
        $arrayField = array(
            'applicationId'=>$this->applicationId,
            'password'=>$this->password,
            'referenceNo'=>$referenceNo,
            'otp'=>$otp
            );
            $jsonObjectFields = json_encode($arrayField);
            //return json_decode($this->sendRequest($jsonObjectFields,$this->server));
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
            $subscriberId = $jsonResponse->subscriberId;
            $subscriptionStatus =  $jsonResponse->subscriptionStatus;
            return json_encode(['statusCode'=>$statusCode,'statusDetail'=>$statusDetail,'subscriberId'=>$subscriberId,'subscriptionStatus'=>$subscriptionStatus]);
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