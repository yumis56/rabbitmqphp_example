#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

session_start();

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$request = array();
$request['type'] = "login";
$request['username'] = $_POST["username"];
$request['password'] = $_POST["password"];


$response = $client->send_request($request);
if ($response) { //as-is, it sends both success and failures
	if ($response['returnCode']){ //this specifies if logn is success (returnCode=1)
		$_SESSION['user']=$request['username'];
		
		header('Location: welcome.php');
		exit();
	}
	else {
		//TODO add an error message for php, not html
		header("Location: login.php");
		exit();
	}
}
else {
	//TODO add an error message for php, not html
	header("Location: login.php");
	exit();

}


//end of file
