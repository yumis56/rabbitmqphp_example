<?php 
//Got Code from this youtube video https://www.youtube.com/watch?v=bj4GFsv3_Yc&ab_channel=ProgrammingWithSpirit : accessed 10/10/2024 11:07AM// 
require_once __DIR__ . '/vendor/autoload.php'; 
use PhpAmqLib/Connection/AMQStreamConnection; 
use PhpAmqLib/Message/AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

$channel->queue_declare('hello', false, false, false, false );

$msg = new AMQPMessage('Hello World'); 
$channel->basic_publish($msg, '', 'hello');

echo " [x] 'test message' \n";

$channel->close();
$connection->close();
?> 
