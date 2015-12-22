<?php 

/*
 * ajax/delete_user_form.php
 */

include_once("../includes/session.inc.php");
include_once("../includes/db.php");
include_once("../includes/site.class.php");
include_once("../lang/lang.{$site_lang}.php");

$csms = new SmppiSite();

include("../includes/auth.inc.php");

if(in_array("SMS_ADMIN", $user_rights)){
	
	if(isset($_REQUEST['id']) && $_REQUEST['id'] > 0){
	
		$html = "
				<form name=\"delete_user\" method=\"post\" action=\"/user.act.php\" class=\"form-horizontal\" role=\"form\">
					<input type=\"hidden\" name=\"del_user_id\" id=\"del_user_id\" value=\"{$_REQUEST['id']}\">
					<button type=\"submit\" class=\"btn btn-danger\">".BTN_DELETE."</button>
				</form>
		";
	
	}
	else{
		$html = "";
	}
	
	echo $html;

}
else{
	exit;
}
