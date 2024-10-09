#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  //$msg = "test message";
  $msg = "ERROR: No message provided. Please specify a message as an argument."
}

//TODO eventually, don't hard-code 'steve' and 'password'
$request = array();
$request['type'] = "Login";
$request['username'] = "steve";
$request['password'] = "password";
$request['message'] = $msg;
$response = $client->send_request($request); //this is for authentication/processing
//$response = $client->publish($request); //sends same request but different method, maybe broadcasting/logging ??

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

