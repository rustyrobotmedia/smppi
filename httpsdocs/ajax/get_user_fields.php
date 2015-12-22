<?php

/*
 * ajax/get_user_fields.php
 */

include_once("../includes/session.inc.php");
include_once("../includes/db.php");
include_once("../includes/site.class.php");
include_once("../lang/lang.{$site_lang}.php");

$csms = new SmppiSite();

include("../includes/auth.inc.php");

if(in_array("SMS_ADMIN", $user_rights)){

	if(isset($_REQUEST["id"]) && $_REQUEST["id"] > 0){
		$id = $_REQUEST["id"];
		$modal_header = "Редактировать пользователя";
		$current_user = $csms->get_users($id);
		$current_rights = $csms->get_rights($id);
		$login = $current_user[0]['login'];
		$ip = $current_user[0]['ip'];
		$interface = $current_user[0]['interface'];
		$rights = array();
		foreach($current_rights as $right){
			$rights[] = array(
						"right" => $right['right'],
						"descr" => $right['descr'],
						"checked" => $right['checked'],
					);
		}
		$interfaces = array(
				$interface,
				"web",
				"api",
		);
		
		
	}
	else{
		$id = 0;
		$modal_header = "Создать пользователя";
		$current_rights = $csms->get_rights();
		$login = "";
		$ip = "";
		$rights = array();
			foreach($current_rights as $right){
				$rights[] = array(
							"right" => $right['right'],
							"descr" => $right['descr'],
							"checked" => $right['checked'],
						);
		}
		$interfaces = array(
				"web",
				"api",
		);
		
	}
	
	$params = array(
			"id"=>$id,
			"modal_header"=>$modal_header,
			"login"=>$login,
			"ip"=>$ip,
			"modal_header"=>$modal_header,
			"rights"=>$rights,
			"interfaces"=>$interfaces,
	);
	
	echo json_encode($params);
	
}
else{
	exit;
}
