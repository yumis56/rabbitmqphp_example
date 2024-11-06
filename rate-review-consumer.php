<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Connect to RabbitMQ server
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// Declare queues for processing rate reviews
$channel->queue_declare('rate_review_queue', false, true, false, false);
$channel->queue_declare('rate_review_response_queue', false, true, false, false);

// Database connection (replace with actual database credentials)
$pdo = new PDO('mysql:host=localhost;dbname=it490', 'test', 'test');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$callback = function($msg) use ($pdo, $channel) {
    // Decode the message to get rate review data
    $rateReviewData = json_decode($msg->body, true);
    $guestName = $rateReviewData['guestName'];
    $review = $rateReviewData['review'];
    $rating = $rateReviewData['rating'];
    $location = $rateReviewData['location'];
    $date = $rateReviewData['date'];

    // Insert rate review data into the database

    $stmt = $pdo->prepare("INSERT INTO reviews (guest_name, review_text, rating, location, date) VALUES ('$guestName', '$review', '$rating', '$location', '$date')");
    $stmt->execute();
      


    // Calculate the average rating for the location (example query)
    $avgStmt = $pdo->prepare("SELECT AVG(rating) as avgRating FROM rate_reviews WHERE location = :location");
    $avgStmt->execute(['location' => $location]);
    $averageRating = $avgStmt->fetch(PDO::FETCH_ASSOC)['avgRating'];

    // Prepare response data
    $response = [
        'status' => 'success',
        'averageRating' => $averageRating,
        'message' => 'Rate review processed successfully!'
    ];

    // Send response to response queue
    $responseJson = json_encode($response);
    $responseMsg = new AMQPMessage($responseJson, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    $channel->basic_publish($responseMsg, '', 'rate_review_response_queue');

    // Acknowledge the request message
    $msg->ack();
};

// Consume messages from rate_review_queue
$channel->basic_consume('rate_review_queue', '', false, false, false, false, $callback);

// Keep the consumer running
while ($channel->is_consuming()) {
    $channel->wait();
}

// Close channel and connection when done
$channel->close();
$connection->close();
?>
