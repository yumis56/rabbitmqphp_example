<?php
require_once 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare the queue
$channel->queue_declare('flight_booking_queue', false, true, false, false);

// Callback function to process each message
$callback = function($msg) {
    $bookingData = json_decode($msg->body, true);

    // Process booking (for example, store in database)
    echo "Processing booking for: " . $bookingData['ticketOwner'] . PHP_EOL;
    echo "From: " . $bookingData['origin'] . " to " . $bookingData['destination'] . PHP_EOL;
    echo "Departure Date: " . $bookingData['departureDate'] . " Return Date: " . $bookingData['returnDate'] . PHP_EOL;

    // Acknowledge message
    $msg->ack();
};

// Set to consume messages
$channel->basic_consume('flight_booking_queue', '', false, false, false, false, $callback);

// Keep listening until script is stopped
while ($channel->is_consuming()) {
    $channel->wait();
}

// Close connection and channel
$channel->close();
$connection->close();
?>
