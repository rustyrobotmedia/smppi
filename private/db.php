<?php

include_once("config.php");

$db = new mysqli ($dbhost, $dbuser, $dbpassword, $dbname);
if (mysqli_connect_errno()){
	echo "\nError: don't connecting on database: $dbname";
	exit();
}
$db->query("set names utf8;");
