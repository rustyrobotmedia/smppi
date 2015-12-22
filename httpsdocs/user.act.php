<?php

/*
 * user.act.php
 */

include_once("includes/session.inc.php");
include_once("includes/db.php");
include_once("includes/site.class.php");
include_once("lang/lang.{$site_lang}.php");

$csms = new SmppiSite();

include("includes/auth.inc.php");

if(in_array("SMS_ADMIN", $user_rights)){

	if(isset($_REQUEST['user_id'])
			&& $_REQUEST['user_id'] == 0
			&& isset($_REQUEST['user_login'])
			&& $_REQUEST['user_login'] != ""
			&& isset($_REQUEST['user_password'])
			&& $_REQUEST['user_password'] != ""
			&& isset($_REQUEST['user_ip'])
			&& $_REQUEST['user_ip'] != ""
			&& isset($_REQUEST['user_interface'])
			&& $_REQUEST['user_interface'] != ""
			){
		$params = array(
			"login" => $_REQUEST['user_login'],
			"password" => md5($_REQUEST['user_password']),
			"ip" => $_REQUEST['user_ip'],
			"interface" => $_REQUEST['user_interface'],
		);
		if($new_user_id = $csms->create_user($params)){
				$csms->users_log($user_id, "create user:{$new_user_id}", $_SERVER['REMOTE_ADDR']);
				if(isset($_REQUEST['user_rights'])){
					foreach ($_REQUEST['user_rights'] as $right){
						$csms->insert_right($new_user_id, $right);
					}
				}
				header("location: /adm/");
			}
		}
	elseif(isset($_REQUEST['user_id'])
			&& $_REQUEST['user_id'] > 0
			&& isset($_REQUEST['user_login'])
			&& $_REQUEST['user_login'] != ""
			&& isset($_REQUEST['user_password'])
			&& isset($_REQUEST['user_ip'])
			&& $_REQUEST['user_ip'] != ""
			&& isset($_REQUEST['user_interface'])
			&& $_REQUEST['user_interface'] != ""
			){
		$user_password = ($_REQUEST['user_password'] == "") ? "" : md5($_REQUEST['user_password']);
		$params = array(
				"id" => $_REQUEST['user_id'],
				"login" => $_REQUEST['user_login'],
				"password" => $user_password,
				"ip" => $_REQUEST['user_ip'],
				"interface" => $_REQUEST['user_interface'],
		);
		if($csms->update_user($params)){
			$new_user_id = $params['id'];
			$csms->users_log($user_id, "update user:{$new_user_id}", $_SERVER['REMOTE_ADDR']);
			$csms->delete_rights($new_user_id);
			if(isset($_REQUEST['user_rights'])){
				foreach ($_REQUEST['user_rights'] as $right){
					$csms->insert_right($new_user_id, $right);
				}
			}
			header("location: /adm/");
		}
	}
	elseif(isset($_REQUEST['del_user_id'])){
		$del_user_id = $_REQUEST['del_user_id'];
		$csms->users_log($user_id, "delete user:{$del_user_id}", $_SERVER['REMOTE_ADDR']);
		$csms->delete_rights($del_user_id);
		$csms->delete_user($del_user_id);
		header("location: /adm/");
	}
	else{
		$_SESSION['user_error'] = USER_FIELDS_ERROR;
		header("location: /adm/");
	}
}
else{
	header("location: /");
}
