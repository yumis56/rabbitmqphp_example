<?php
session_start();
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$bookingRequest = array();
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connect to RabbitMQ server
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare a queue to send messages to
$channel->queue_declare('booking_queue', false, true, false, false);
// Database connection taken from testRabbitMQServer.php
    $host = 'node4';
    $dbUser = 'test';
    $dbPass = 'test';
    $dbName = 'it490';

    // Create a database connection
    $conn = mysqli_connect($host, $dbUser, $dbPass, $dbName);

// Collect form data
$bookingData = [
    'guestName' => $_POST['guestName'] ?? '',
    'numGuest' => $_POST['numGuest'] ?? '',
    'checkinDate' => $_POST['checkinDate'] ?? '',
    'checkinTime' => $_POST['checkinTime'] ?? '',
    'checkoutDate' => $_POST['checkoutDate'] ?? '',
    'checkoutTime' => $_POST['checkoutTime'] ?? '',
    'hotelName' => $_POST['hotelName'] ?? ''  // Corrected from 'location' to 'hotelName'
];

// Convert booking data to JSON
$dataJson = json_encode($bookingData);

// Create a message with the data
$message = new AMQPMessage($dataJson, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

// Publish the message to the queue
$channel->basic_publish($message, '', 'booking_queue');

// Close the channel and connection
$channel->close();
$connection->close();

// Redirect or display a success message
$_SESSION['message'] = 'Booking submitted successfully!';
header('Location: bookingHotel.php');
exit;
