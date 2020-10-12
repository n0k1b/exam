<?php

namespace App\Classes;
use Exception;
use App\Classes\Core;

class SMSSender  extends Core{
	private $applicationId,
			$password,
			$charging_amount,
			$encoding,
			$version,
			$deliveryStatusRequest,
			$binaryHeader,
			$sourceAddress,
			$serverURL;
	
	/* Send the server name, app password and app id
	*	Dialog Production Severurl : HTTPS : - https://api.dialog.lk/sms/send
	*				     HTTP  : - http://api.dialog.lk:8080/sms/send
	*/		
	public function __construct($serverURL, $applicationId, $password)
	{
		if(!(isset($serverURL, $applicationId, $password)))
			throw new SMSServiceException('Request Invalid.', 'E1312');
		else {
			$this->applicationId = $applicationId;
			$this->password = $password;
			$this->serverURL = $serverURL;
		}
	}
	
	
	public function send_otp($address){
		if(empty($address))
			throw new SMSServiceException('Address must be fillable', 'E1325');
		else {
			$jsonStream = (is_string($addresses))?$this->resolveJsonStream($message, array($addresses)):(is_array($addresses)?$this->resolveJsonStream($message, $addresses):null);
			//return ($jsonStream!=null)?$this->handleResponse( $this->sendRequest($jsonStream,$this->serverURL) ):false;
			$a = $this->handleResponse( $this->sendRequest($jsonStream,$this->serverURL));
			return $a;
		
		}
	}
	
	private function handleResponse($jsonResponse){
	    //file_put_contents("handleresponse.txt",$jsonResponse);
	  $response = json_decode($jsonResponse);
	
	  $statusCode = $response->statusCode;
	
		$statusDetail = 'Request was successfully processed';
		
		
		if(empty($jsonResponse))
			throw new SMSServiceException('Invalid server URL', '500');
		else if(strcmp($statusCode, 'S1000')==0)
			return $statusCode;
		else
		    return $statusCode;
			//throw new SMSServiceException($statusDetail, $statusCode);
	}
	
	private function resolveJsonStream($message, $addresses){
		
		$messageDetails = array("message"=>$message,
	   	           				"destinationAddresses"=>$addresses
           					);
		
		if (isset($this->sourceAddress)) {
			$messageDetails= array_merge($messageDetails,array("sourceAddress" => $this->sourceAddress));   
		}
		
		if (isset($this->deliveryStatusRequest)) {
			$messageDetails= array_merge($messageDetails,array("deliveryStatusRequest" => $this->deliveryStatusRequest));
		}
		
		if (isset($this->binaryHeader)) {
			$messageDetails= array_merge($messageDetails,array("binaryHeader" => $this->binaryHeader));
		}	
		
		if (isset($this->version)) {
			$messageDetails= array_merge($messageDetails,array("version" => $this->version)); 
		}	
		
		if (isset($this->encoding)) {
			$messageDetails= array_merge($messageDetails,array("encoding" => $this->encoding)); 
		}
		
		$applicationDetails = array('applicationId'=>$this->applicationId,
						 'password'=>$this->password,);
		
		$jsonStream = json_encode($applicationDetails+$messageDetails);
		
		return $jsonStream;
	}

	public function setsourceAddress($sourceAddress){
		$this->sourceAddress=$sourceAddress;
	}

	public function setcharging_amount($charging_amount){
		$this->charging_amount=$charging_amount;
	}

	public function setencoding($encoding){
		$this->encoding=$encoding;
	}

	public function setversion($version){
		$this->version=$version;
	}

	public function setbinaryHeader($binaryHeader){
		$this->binaryHeader=$binaryHeader;
	}

	public function setdeliveryStatusRequest($deliveryStatusRequest){
		$this->deliveryStatusRequest=$deliveryStatusRequest;
	}
}
