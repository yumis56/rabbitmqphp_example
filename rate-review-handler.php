<?php
session_start();
require 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// Database connection settings
$dsn = 'mysql:host=node4;dbname=it490';
$username = 'test';
$password = 'test';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (Exception $e) {
    $_SESSION['message'] = "Database connection failed: " . $e->getMessage();
    header('Location: rate-review.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data with additional validation
    $rateReviewData = [
        'guestName' => $_POST['guestName'] ?? '',
        'review' => $_POST['review'] ?? '',
        'rating' => $_POST['rating'] ?? '',
        'location' => $_POST['location'] ?? '',
        'date' => $_POST['date'] ?? ''
    ];

    // Check required fields
    foreach ($rateReviewData as $key => $value) {
        if (empty($value)) {
            $_SESSION['message'] = "Please fill in all required fields.";
            header('Location: rate-review.php');
            exit;
        }
    }

    // Check if photo is uploaded
    $fileContent = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileContent = file_get_contents($fileTmpPath);
    } else{
    
        $_SESSION['message'] = "ERROR: Failed to upload selected file.";
    
    }

    // RabbitMQ message setup
    try {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // Declare queues
        $channel->queue_declare('rate_review_queue', false, true, false, false);
        $channel->queue_declare('rate_review_response_queue', false, true, false, false);

        // Convert review data to JSON
        $dataJson = json_encode($rateReviewData);

        // Create and publish the message
        $message = new AMQPMessage($dataJson, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($message, '', 'rate_review_queue');

        // Listen for a response in the response queue
        $response = null;
        $callback = function($responseMsg) use (&$response) {
            $response = json_decode($responseMsg->body, true);
            $_SESSION['responseData'] = $response;
            $responseMsg->ack(); 
        };
        $channel->basic_consume('rate_review_response_queue', '', false, false, false, false, $callback);

        // Wait for response with timeout to avoid infinite waiting
        $timeout = 10;
        $start = time();
        while (!$response && $channel->is_consuming() && (time() - $start) < $timeout) {
            $channel->wait(null, false, 1);
        }
        

        $_SESSION['message'] = $response ? 'Rate review submitted successfully!' : 'No response received from review service.';
  

    } catch (Exception $e) {
        $_SESSION['message'] = 'Error sending review to RabbitMQ: ' . $e->getMessage();
    } finally {
          // Close RabbitMQ connection
          if ($channel){
            $channel->close();
          }
        $connection->close();
          
    }

    // Store review and optional photo data in the database
    try {
        $sql = "INSERT INTO reviews (guestName, review, rating, location, visitDate" . ($fileContent ? ", photo_data" : "") . ") 
                VALUES (:guestName, :review, :rating, :location, :date" . ($fileContent ? ", :photo" : "") . ")";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':guestName', $rateReviewData['guestName']);
        $stmt->bindParam(':review', $rateReviewData['review']);
        $stmt->bindParam(':rating', $rateReviewData['rating']);
        $stmt->bindParam(':location', $rateReviewData['location']);
        $stmt->bindParam(':date', $rateReviewData['date']);
        
        if ($fileContent) {
            $stmt->bindParam(':photo', $fileContent, PDO::PARAM_LOB);
        }


        $_SESSION['message'] .= $stmt->execute() ? " Review saved to the database successfully!" : " Error saving review to the database.";

    } catch (Exception $e) {
        $_SESSION['message'] .= ' Database error: ' . $e->getMessage();
    }

}

// Redirect back to the review page
header('Location: rate-review.php');
exit();
