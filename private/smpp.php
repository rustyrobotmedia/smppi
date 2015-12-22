<?php

/*
 * private/smpp.php
 */

function smpp_send($smpp_hosts,$smpp_port,$smpp_login,$smpp_password,$smpp_from,$smpp_to,$smpp_msg){

	require_once 'smpp/smppclient.class.php';
	//require_once 'smpp/gsmencoder.class.php';
	require_once 'smpp/sockettransport.class.php';
	
	// Construct transport and client
	$transport = new SocketTransport($smpp_hosts,$smpp_port);
	$transport->setRecvTimeout(60000);
	$smpp = new SmppClient($transport);
	
	$tags = "CSMS_16BIT_TAGS";
	$data_coding = SMPP::DATA_CODING_ISO8859_5; // Cyrillic
	
	// Activate binary hex-output of server interaction
	$smpp->debug = false;
	$transport->debug = false;
	
	// Open the connection
	$transport->open();
	$smpp->bindTransmitter($smpp_login,$smpp_password);
	
	// Optional connection specific overrides
	//SmppClient::$sms_null_terminate_octetstrings = false;
	//SmppClient::$csms_method = SmppClient::CSMS_PAYLOAD;
	//SmppClient::$sms_registered_delivery_flag = SMPP::REG_DELIVERY_SMSC_BOTH;
	
	// Prepare message
	$message = $smpp_msg;
	//$encodedMessage = GsmEncoder::utf8_to_gsm0338($message);
	//$encodedMessage = iconv("UTF-8","UCS-2",$message);
	$encodedMessage = $message;
	$from = new SmppAddress($smpp_from,SMPP::TON_ALPHANUMERIC);
	$to = new SmppAddress($smpp_to,SMPP::TON_INTERNATIONAL,SMPP::NPI_E164);
	
	// Send
	if($smpp_id = $smpp->sendSMS($from,$to,$encodedMessage,$tags,$data_coding)){
		// Close connection
		$smpp->close();
		
		return trim($smpp_id); 
	}
	else{
		// Close connection
		$smpp->close();
		
		return false;
	}
}
