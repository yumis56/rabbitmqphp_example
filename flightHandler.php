<?php
session_start();

//NOTE: Copied from Dr. Kehoe's code, IMPORTANT: this is the code that is primarily used to 1. store the data from the forms and 2. send the array where the form data is stored to the MQ
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$flightBook = array();


// Database connection inspired from IT202 Fall 2023 with Professor Matthew Toegel. PD438 10/30/2024//
$dsn = 'mysql:host=localhost;dbname=it490';
$username = 'test';
$password = 'test';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$pdo = new PDO($dsn, $username, $password, $options);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get review text
    $reviewText = $_POST['review_text']; //ticketOwner
    $reviewText = $_POST['review_text']; //origin 
    $reviewText = $_POST['review_text']; //destination 
    $reviewText = $_POST['review_text']; //departureDate
    $reviewText = $_POST['review_text']; // returnDate 
    

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
   

    // Redirect back to the form page
    header("Location: rate-review.php");
    exit();
}
?> 