<?php

/*
 * private/pc_send.php
 * 
 * send sms by php/pcntl
 */

$pid_file = "/tmp/sms_send.pid";

$act = "";
if(isset($argv[1])) $act = $argv[1];

// STOP
if($act == "stop"){
	if(file_exists($pid_file)){
		$pid = file_get_contents($pid_file);
		print "stop $pid\n";
		posix_kill($pid, SIGTERM);
		unlink($pid_file);
	}
	exit;
}

// START
elseif($act == "start"){
	
	if(file_exists($pid_file)){
		if($check = file_get_contents($pid_file)){
			print "stop $check\n";
			posix_kill($check, SIGTERM);
		}
		unlink($pid_file);
	}
	
	declare(ticks=1);
	
	$pid = pcntl_fork();
		
	if ($pid == -1) {
		die("could not fork");
	}
	else if ($pid) {
		exit(); // we are the parent
	}
	else {
		// we are the child
		$spid = posix_getpid();
		if(file_put_contents($pid_file, $spid)){
			print "start $spid\n";
			
			// move to loop
			//require_once("config.php");
			//require_once("smpp/smppclient.class.php");
			//require_once("smpp/sockettransport.class.php");
			//require_once("db.php");
			//require_once("log.php");
			
		}
		else{
			print "can't create pid file\n";
			exit;
		}
	}
	
	// detatch from the controlling terminal
	if (posix_setsid() == -1) {
		die("could not detach from terminal");
	}
	
	// setup signal handlers
	pcntl_signal(SIGTERM, "sig_handler");
	pcntl_signal(SIGHUP, "sig_handler");
	
	// loop forever performing tasks
	while (true) {
		
		// from fork
		require_once("config.php");
		require_once("smpp/smppclient.class.php");
		require_once("smpp/sockettransport.class.php");
		require_once("db.php");
		require_once("log.php");
		
		sms_send_gsm(PATH_OUTGOUING);
		check_sent_gsm(PATH_SENT);
		sms_send_smpp($smpp_hosts, $smpp_port, $smpp_login, $smpp_password, $smpp_from);
		sleep(1);
	
	}
	
}

// OTHER
else{
	print "nosignal, use start|stop\n";
	exit;
	
}

function sms_send_gsm($path_outgoing){
	// gsm send
	
	global $db;
	
	$select = "select id, phonenumber, msg from sms where direction = 1 and process = 0 and method = 'gsm';";
	if($result = $db->query($select)){
		while($row = $result->fetch_assoc()){
			
			$id = $row['id'];
			$phonenumber = check_phone($row['phonenumber']);
			$msg = $row['msg'];
			
			if(preg_match("/[А-Яа-я]+/", $msg)){
				$add = "\nAlphabet: Unicode";
				$msg = iconv("UTF-8","UCS-2BE",$msg);
			}
			else{
				$add = "";
			}
			
			$file_src = "To: +{$phonenumber}{$add}\n\n{$msg}";
			
			if(DEBUG == 1) log2file("pcntl_send", "echo \"{$file_src}\" > {$path_outgoing}sms{$id}");
			file_put_contents($path_outgoing."sms{$row['id']}", $file_src);
			$full_msg = $db->real_escape_string($file_src);
			$update = "update sms set full_msg = '{$full_msg}', dt=now(), process = 1 where id = {$id}";
			if(!$db->query($update)){
				log2file("mysql_error",$db->error);
			}
		}
	}
	
}

function check_sent_gsm($path_sent){
	
	global $db;
	
	$select = "select id from sms where direction = 1 and process = 1 and result is null and method = 'gsm' and tstamp > now() - interval 1 day;";
	if($result = $db->query($select)){
		while($row = $result->fetch_assoc()){
			$id = $row['id'];
			if(file_exists($path_sent."sms".$id)){
				$file = file_get_contents($path_sent."sms".$id);
				$lines = explode("\n", $file);
				foreach($lines as $line){
					if($line != "" && strpos($line,": ") > 0){
						list($param,$value) = explode(": ",$line);
						if($param == "Message_id") $int_id = $value;
					}
				}
				$db->query("update sms set full_msg = '{$file}', result = 'sent' where id = '{$id}';");
				if(isset($int_id)) $db->query("update sms set int_id = '{$int_id}' where id = '{$id}';");
			}
		}
	}	
	
}

function sms_send_smpp($smpp_hosts, $smpp_port, $smpp_login, $smpp_password, $smpp_from){
	// smpp send
	
	global $db;
	
	$select = "select id, phonenumber, msg from sms where direction = 1 and process = 0 and method = 'smpp';";
	if($result = $db->query($select)){
		
		while($row = $result->fetch_assoc()){
			
			$id = $row['id'];
			$phonenumber = check_phone($row['phonenumber']);
			$msg = $row['msg'];
			
			$result_sms = smpp_send($smpp_hosts, $smpp_port, $smpp_login, $smpp_password, $smpp_from, $phonenumber, $msg);
			
			if(DEBUG == 1) log2file("pcntl_send smpp", $result_sms);
			$update = "update sms set full_msg = '{$result_sms}', dt=now(), process = 1 where id = {$id}";
			if(!$db->query($update)){
				log2file("mysql_error",$db->error);
			}
			
		}
	}
}

function smpp_send($smpp_hosts,$smpp_port,$smpp_login,$smpp_password,$smpp_from,$smpp_to,$smpp_msg){

	$transport = new SocketTransport($smpp_hosts,$smpp_port);
	$transport->setRecvTimeout(60000);
	$smpp = new SmppClient($transport);

	$tags = "CSMS_16BIT_TAGS";
	$data_coding = SMPP::DATA_CODING_ISO8859_5; // Cyrillic

	$smpp->debug = false;
	$transport->debug = false;

	$transport->open();
	$smpp->bindTransmitter($smpp_login,$smpp_password);

	$message = $smpp_msg;
	$encodedMessage = $message;
	$from = new SmppAddress($smpp_from,SMPP::TON_ALPHANUMERIC);
	$to = new SmppAddress($smpp_to,SMPP::TON_INTERNATIONAL,SMPP::NPI_E164);

	if($smpp_id = $smpp->sendSMS($from,$to,$encodedMessage,$tags,$data_coding)){
		$smpp->close();

		return trim($smpp_id);
	}
	else{
		$smpp->close();

		return false;
	}
}

function check_phone($phonenumber){

	/*
	 * check RU phonenumbers
	 * 
	 * string
	 */

	$phonenumber = preg_replace("/\D/", "", $phonenumber);

	if(strlen($phonenumber) >= 10){
		$phonenumber = "7".substr($phonenumber, -10);
	}

	return $phonenumber;

}

function sig_handler($signo){
	
	switch ($signo) {
		case SIGTERM:
			// handle shutdown tasks
			exit;
			break;
		case SIGHUP:
			// handle shutdown tasks
			exit;
			break;
		default:
			// handle all other signals
	}
}

