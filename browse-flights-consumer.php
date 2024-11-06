<?php
require 'vendor/autoload.php';
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Connect to RabbitMQ server
$connection = new AMQPStreamConnection('localhost', 5672, 'test', 'test');
$channel = $connection->channel();

// Declare the queue to consume from
$channel->queue_declare('booking_queue', false, true, false, false);

// Database connection (ensure credentials match your setup)
$dsn = 'mysql:host=node2;dbname=it490';
$username = 'test';
$password = 'test';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$pdo = new PDO($dsn, $username, $password, $options);

// Callback function to process messages
$callback = function($msg) use ($pdo) {
    echo "Received booking data: ", $msg->body, "\n";
    
    // Decode JSON data
    $bookingData = json_decode($msg->body, true);
    
    // Prepare and execute SQL query to save booking data to database
    $sql = "INSERT INTO bookings (guestName, numGuest, checkinDate, checkinTime, checkoutDate, checkoutTime, hotelName)
            VALUES (:guestName, :numGuest, :checkinDate, :checkinTime, :checkoutDate, :checkoutTime, :hotelName)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':guestName' => $bookingData['guestName'],
        ':numGuest' => $bookingData['numGuest'],
        ':checkinDate' => $bookingData['checkinDate'],
        ':checkinTime' => $bookingData['checkinTime'],
        ':checkoutDate' => $bookingData['checkoutDate'],
        ':checkoutTime' => $bookingData['checkoutTime'],
        ':hotelName' => $bookingData['hotelName']
    ]);

    echo "Booking saved to database.\n";
    
    // Acknowledge the message
    $msg->ack();
};

// Set up consumer
$channel->basic_qos(null, 1, null);
$channel->basic_consume('booking_queue', '', false, false, false, false, $callback);

// Keep the script running to listen for incoming messages
while($channel->is_consuming()) {
    $channel->wait();
}

// Close the channel and connection
$channel->close();
$connection->close();
?>
