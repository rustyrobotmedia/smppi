<?php

include_once("includes/session.inc.php");
include_once("includes/db.php");
include_once("includes/site.class.php");
include_once("lang/lang.{$site_lang}.php");

$csms = new SmppiSite();

include("includes/auth.inc.php");

if(in_array("SMS_WEBSEND", $user_rights)){

	if(isset($_REQUEST['phone']) && isset($_REQUEST['msg'])){
		$_REQUEST['phone'] = preg_replace("/\D/", "", $_REQUEST['phone']);
		$translit = (isset($_REQUEST['translit']) && $_REQUEST['translit'] == 1) ? 1 : 0;
		$method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : "gsm";
		if($_REQUEST['phone'] != "" && $_REQUEST['msg'] != ""){
			if($sms_id = $csms->sendsms($_REQUEST['phone'],$_REQUEST['msg'],$translit,$method)){
				$csms->users_log($user_id, "sendsms:{$sms_id}", $_SERVER['REMOTE_ADDR']);
				header("location: /outgoing/");
			}
		}
		else{
			$_SESSION['send_error'] = SEND_ERROR;
			header("location: /send/");
		}
		
	}
	
}
else{
	header("location: /");
}
