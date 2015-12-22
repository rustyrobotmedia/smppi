<?php
/*
 * api.php
 */

include("includes/site.class.php");

$csms = new SmppiSite();

if(isset($_REQUEST['login']) && isset($_REQUEST['skey']) && isset($_REQUEST['action'])){
	
	$ip = $_SERVER['REMOTE_ADDR'];
	$login = $_REQUEST['login'];
	$md5key = md5($_REQUEST['skey']);
	
	if($user_id = $csms->api_auth($login, $md5key, $ip)){
		
		if($_REQUEST['action'] == "sendsms"){
			
			$user_rights = $csms->user_rights($user_id);
				if(in_array("SMS_APISEND", $user_rights)){
				$phone = $_REQUEST['phone'];
				$msg = $_REQUEST['msg'];
				$method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : "gsm";
				if($sms_id = $csms->sendsms($phone, $msg, 1, $method)){
					$csms->users_log($user_id, "sendsms:{$sms_id}", $_SERVER['REMOTE_ADDR']);
					$return = array(
							"result" => "success",
							"sms_id" => $sms_id,
					);
				}
				else{
					$return = array(
							"result" => "error",
							"reason" => "sendsms",
					);
				}
			}
			else{
				$return = array(
						"result" => "error",
						"reason" => "access denied",
				);
			}
			
		}
		else{
			$return = array(
					"result" => "error",
					"reason" => "wrong request 2",
			);
		}
		
	}
	else{
		$return = array(
				"result" => "error",
				"reason" => "bad login or password",
		);
	}
}
else{
		$return = array(
				"result" => "error",
				"reason" => "wrong request 1",
		);
	}

$xmlstr = "<answer/>";
$xml = new SimpleXMLElement($xmlstr);
foreach ($return as $return_key => $return_value){
	$xml->addChild($return_key,$return_value);
}
echo $xml->asXML();
