<?php

/*
 * private/pc_receive.php
 * 
 * sms receive by php/pcntl
 */

$pid_file = "/tmp/sms_receive.pid";

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
			
			require_once("config.php");
			require_once("smpp/smppclient.class.php");
			require_once("smpp/sockettransport.class.php");
			require_once("db.php");
			require_once("log.php");
			
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
		
		wait_read_gsm(PATH_INCOMING,PATH_RECEIVED);
		//wait_read_smpp($smpp_hosts,$smpp_port,$smpp_login,$smpp_password,10000); // TODO
		sleep(1);
	
	}
	
}

// OTHER
else{
	print "nosignal, use start|stop\n";
	exit;
	
}

function wait_read_gsm($path_incoming,$path_received){
	
	global $db;
	
	$d = dir($path_incoming);
	while (false !== ($entry = $d->read())) {
		if($entry != "." && $entry != ".."){
			$file = $entry;
			$full_msg = file_get_contents($path_incoming.$file);
	
			// парсим файл
			$smsfile = parse_sms_file($full_msg);
			$phone = $smsfile['From'];
			$dt = $smsfile['Sent'];
			$alphabet = $smsfile['Alphabet'];
	
			if(isset($alphabet) && $alphabet == "UCS2"){
				$msg = iconv("UCS-2BE", "UTF-8", $smsfile['Msg']);
			}
			else{
				$msg = $smsfile['Msg'];
			}
	
			$full_msg = $db->real_escape_string($full_msg);
			$msg = $db->real_escape_string($msg);
			
			if($msg != "SMS STATUS REPORT"){
				$insert = "insert into sms set dt = from_unixtime(unix_timestamp('{$dt}')), phonenumber = '{$phone}', msg=trim('{$msg}'), full_msg = '{$full_msg}';";
				if(DEBUG == 1) log2file("pcntl_receive", $insert);
				if($db->query($insert)){
					rename($path_incoming.$file, $path_received.$file);
				}
				else{
					log2file("mysql_error".$db->query);
				}
			}
			else{
				$int_id = $smsfile['Message_id'];
				$state = $smsfile['Status']['code'];
				$update = "update sms set result = '{$state}' where int_id = '{$int_id}';";
				if(DEBUG == 1) log2file("pcntl_receive", $update);
				if($db->query($update)){
					rename($path_incoming.$file, $path_received.$file);
				}
				else{
					log2file("mysql_error".$db->query);
				}
			}
		}
	}
	$d->close();
}

function parse_sms_file($filecontent){
	
	$result = array();
	$result['Msg'] = "";
	
	$lines = explode("\n", $filecontent);
	$i=0;
	if(strpos($filecontent,"SMS STATUS REPORT") === false){
		foreach ($lines as $line){
			if($line != ""){
				if($i < 12){
					$parse = explode(":",$line);
					$result[$parse[0]] = trim($parse[1]);
				}
				else{
					$result['Msg'] .= $line."\n";
				}
			}
			$i++;
		}
		$result['Msg'] = substr($result['Msg'],0,-1);
	}
	else{
		foreach ($lines as $line){
			if($line != ""){
				if($i == 12){
					$result['Msg'] .= $line;
				}
				else{
					$parse = explode(":",$line);
					if($parse[0] != "Status"){
						$result[$parse[0]] = trim($parse[1]);
					}
					else{
						$stats = explode(",",trim($parse[1]));
						$result[$parse[0]] = array(
								"code" => $stats[0],
								"text" => $stats[1],
								"descr" => $stats[2],
						);
					}
				}
			}
			$i++;
		}
	}
	
	return $result;
	
}

function wait_read_smpp($smpp_hosts,$smpp_port,$smpp_login,$smpp_password,$wait=60000){
	
	/*
	 * TODO: not complited!
	 */

	// Construct transport and client
	$transport = new SocketTransport($smpp_hosts,$smpp_port);
	$transport->setRecvTimeout($wait); // for this example wait up to 60 seconds for data
	$smpp = new SmppClient($transport);

	// Activate binary hex-output of server interaction
	$smpp->debug = false;
	$transport->debug = false;

	// Open the connection
	$transport->open();
	$smpp->bindReceiver($smpp_login,$smpp_password);

	// Read SMS and output
	$sms = $smpp->readSMS();
	echo "SMS:\n";
	var_dump($sms);

	// Close connection
	$smpp->close();
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

