<?php

if(isset($_SESSION['key'])){

	if($user_id = $csms->user_check_key($_SESSION['key'],$_SERVER['REMOTE_ADDR'])){
		$user_rights = $csms->user_rights($user_id);
	}
	
	if(!in_array("SMS_ACCESS", $user_rights)){
		unset($_SESSION['key']);
		$auth_error = ACCESS_DENIED;
		include("templates/auth.tpl.php");
		echo $login_html;
		exit;
	}
	
}
elseif(isset($_REQUEST['login']) && isset($_REQUEST['password']) && $_REQUEST['login'] != "" && $_REQUEST['password'] != ""){
	
	$login = $_REQUEST['login'];
	$password = md5($_REQUEST['password']);
	
	if($user_id = $csms->user_auth($login,$password)){
		$_SESSION['key'] = $csms->user_get_key($user_id,$_SERVER['REMOTE_ADDR']);
		$user_rights = $csms->user_rights($user_id);
		$csms->users_log($user_id, "login",$_SERVER['REMOTE_ADDR']);
	}
	else{
		unset($_SESSION['key']);
		$csms->users_log(0, "error:{$_REQUEST['login']}",$_SERVER['REMOTE_ADDR']);
		$auth_error = WRONG_USERNAME_OR_PASSWORD;
		include("templates/auth.tpl.php");
		echo $login_html;
		exit;
	}
	
	if(!in_array("SMS_ACCESS", $user_rights)){
		unset($_SESSION['key']);
		$auth_error = ACCESS_DENIED_BY_LOGIN;
		include("templates/auth.tpl.php");
		echo $login_html;
		exit;
	}
	
}
else{
	unset($_SESSION['key']);
	$auth_error = "";
	include("templates/auth.tpl.php");
	echo $login_html;
	exit;
}

if(isset($_REQUEST['exit'])){
	$csms->users_log($user_id, "exit",$_SERVER['REMOTE_ADDR']);
	unset($_SESSION['key']);
	$auth_error = "";
	include("templates/auth.tpl.php");
	echo $login_html;
	exit;
}
