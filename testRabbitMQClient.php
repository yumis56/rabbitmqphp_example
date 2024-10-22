#!/usr/bin/php
<?php
//debug
echo "|| TEST: testRabbitMQClient.php is running! ||";

//

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$request = array();
$request['type'] = "login";
$request['username'] = $_POST["username"];
$request['password'] = $_POST["password"];
//
//$msg = "welcome back my skibidi sigmas";
//$request['message'] = $msg;
//echo '<pre>' ; print_r($request); echo '</pre';




$response = $client->send_request($request);
if ($response) {
	echo '<pre>' . print_r($response, true) . '</pre';
}
else {

$response['message'] = "ERROR: no resposne from RabbitMQ";
echo '<pre>' ; print_r($response); echo '</pre';

}


//end of file
