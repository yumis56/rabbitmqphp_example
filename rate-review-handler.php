<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

session_start();

//this file is based on testRabbitMQClient.php

//$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
//TODO is client not already running from login?

guest_name
rating
review_text
location
visit_date
photo

$request = array();
$request['type'] = "review";
$request['guest_name'] = $_POST["guest_name"];
$request['rating'] = $_POST["rating"];
$request['review_text'] = $_POST["review_text"];
$request['location'] = $_POST["location"];
$request['visit_date'] = $_POST["visit_date"];
if (isset($_POST['photo']) && !empty($_POST['photo'])){
    $request['photo'] = $_POST["photo"];
}

$response = $client->send_request($request);
if ($response) { //as-is, it sends both success and failures
	if ($response['returnCode']){
		$_SESSION['message']='Successfully submitted review!';
		exit();
	}
	else {
		//TODO add an error message for php, not html
		header("Location: rate-review.php");
		exit();
	}
}
else {
	//TODO add an error message for php, not html
	header("Location: rate-review.php");
	exit();

}


