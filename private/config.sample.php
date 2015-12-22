<?php

/*
 * rename this file into config.php
 */

$dbhost = "";
$dbuser = "";
$dbpassword = "";
$dbname = "smstools";


//devino sms
$smpp_hosts = array(
		"host1",
		"host2",
);
$smpp_port = "2775";
$smpp_login = "";
$smpp_password = "";
$smpp_from = "";

define("DEBUG","1");
define("PATH_LOG","/var/log/smsd/");
define("PATH_INCOMING","/var/spool/sms/incoming/");
define("PATH_RECEIVED","/var/spool/sms/received/");
define("PATH_OUTGOUING","/var/spool/sms/outgoing/");
define("PATH_SENT","/var/spool/sms/sent/");
