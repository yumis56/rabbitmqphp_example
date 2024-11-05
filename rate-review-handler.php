<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Start a session to handle messages
session_start();


//NOTE: Copied from Dr. Kehoe's code, IMPORTANT: this is the code that is primarily used to 1. store the data from the forms and 2. send the array where the form data is stored to the MQ
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$requestReview = array();


// Database connection inspired from IT202 Fall 2023 with Professor Matthew Toegel. PD438 10/30/2024//
$dsn = 'mysql:host=localhost;dbname=your_database';
$username = 'admin';
$password = '12345';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$pdo = new PDO($dsn, $username, $password, $options);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get review text
    $reviewText = $_POST['review_text'];
    $reviewNum = $_POST['rating']

    // Check if the file upload is valid

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

        // Get file data
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $fileContent = file_get_contents($fileTmpPath);


        $requestReview['type'] = "rateAndReview";
        $requestReview['review'] = $reviewText;
        $requestReview['rating'] = $reviewNum;

        $response = $client->send_request($requestReview); //sends data up the MQ

        echo $response //just to test 

        // Insert data into database
        //Maybe insert the database code in a seperate file? instead of having the handler be in the front-end
        /*
        $stmt = $pdo->prepare("INSERT INTO reviews (review_text, photo_name, photo_size, photo_type, photo_data) VALUES (:reviewText, :fileName, :fileSize, :fileType, :fileContent)");
        $stmt->bindParam(':reviewText', $reviewText);
        $stmt->bindParam(':fileName', $fileName);
        $stmt->bindParam(':fileSize', $fileSize);
        $stmt->bindParam(':fileType', $fileType);
        $stmt->bindParam(':fileContent', $fileContent, PDO::PARAM_LOB);

        // Check if data insertion was successful
        if ($stmt->execute()) {
            $_SESSION['message'] = "Review and photo submitted successfully!";
        } else {
            $_SESSION['message'] = "Error saving review and photo.";
        }
    } else {
        $_SESSION['message'] = "Error with the uploaded photo.";
         */ 
       
    }


try {
    // Connect to RabbitMQ server
    $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    $channel = $connection->channel();

    // Declare queues: one for sending rate review requests, one for receiving responses
    $channel->queue_declare('rate_review_queue', false, true, false, false);
    $channel->queue_declare('rate_review_response_queue', false, true, false, false);

    // Collect form data for the rate review
    $rateReviewData = [
        'guestName' => $_POST['guestName'] ?? '',
        'review' => $_POST['review'] ?? '',
        'rating' => $_POST['rating'] ?? '',
        'location' => $_POST['location'] ?? '',
        'date' => $_POST['date'] ?? ''
    ];

    // Convert review data to JSON
    $dataJson = json_encode($rateReviewData);

    // Create a message with the data
    $message = new AMQPMessage($dataJson, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

    // Publish the message to the request queue
    $channel->basic_publish($message, '', 'rate_review_queue');

    // Listen for a response in the rate_review_response_queue
    $response = null;
    $callback = function($responseMsg) use (&$response) {
        $response = json_decode($responseMsg->body, true);
        $_SESSION['responseData'] = $response;
        $responseMsg->ack(); // Acknowledge the response message
    };

    // Consume one message from the response queue and wait for the callback
    $channel->basic_consume('rate_review_response_queue', '', false, false, false, false, $callback);

    // Wait for the response from the response queue
    while (!$response && $channel->is_consuming()) {
        $channel->wait();
    }

    // Close the channel and connection
    $channel->close();
    $connection->close();

    // Set success message or store response data
    if ($response) {
        $_SESSION['message'] = 'Rate review submitted successfully!';
    } else {
        $_SESSION['message'] = 'No response received from review service.';
    }


} catch (Exception $e) {
    // If there's an error, set an error message
    $_SESSION['message'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the review page
header('Location: reviewpage.php');
exit;
?>