#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doReview($formData) {

    //this is based on testRabbitMQServer.php doLogin
  
      $host = 'node4';
      $dbUser = 'test';
      $dbPass = 'test';
      $dbName = 'it490';
  
      // Create a database connection
    
      $conn = mysqli_connect($host, $dbUser, $dbPass, $dbName);
      
      // Check for connection errors
      if ($conn->connect_error) {
          return array("returnCode" => '0', 'message' => "Database connection error");
      }
  
      
          $guest = $formData['guest_name'];  
          $rating = $formData['rating'];  
          $review = $formData['review_text'];
          $location = $formData['location'];
          $visit_date = $formData['visit_date'];
/*
	  if (isset($formData['photo'])){
            $photo = $formData['photo'];
          } //TODO photo is not implemented yet
*/ 
	  // Prepare and bind the SQL statement
       try{
          $stmt = $conn->prepare("INSERT INTO reviews ('guest_name', 'rating', 'review_text', 'location', 'visit_date') VALUES (?, ?, ?, ?, ?)");
          $stmt->bind_param("sisss", $guest, $rating, $review, $location, $visit_date); // 's' specifies the variable type => 'string'
          //TODO make sure the variables are correct, e.g. if it's okay that date is string here
      
          if ($stmt->execute()) {
              return array("returnCode" => '1', 'message' => "SUCCESS: review submitted!");
          }
         //implied else 
	  return array("returnCode" => '0', 'message' => "ERROR: could not submit review.");
          
          
      } /*catch (Exception $e) {
      	
              return array("returnCode" => '0', 'message' => "ERROR: could not submit review.");
	      exit();
      } */finally {
          //close afterwards or else you're causing a DoS, no good!
          if ($stmt) {
              $stmt->close();
          }
	  $conn->close();
	  exit();
      }
      
      return array("returnCode" => '0', 'message' => "ERROR: could not submit review.");
      exit();
}

?>
