<?php
session_start(); 
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Connect to RabbitMQ server
$connection = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$channel = $connection->channel();


//Get the User Input
$request = array();
$request['type'] = "login";
$request['username'] = $_POST["username"];
$request['password'] = $_POST["password"];


$response = $client->send_request($request);

// Declare queues for processing rate reviews
$channel->queue_declare('rate_review_queue', false, true, false, false);
$channel->queue_declare('rate_review_response_queue', false, true, false, false);

// Database connection (replace with actual database credentials)
$pdo = new PDO('mysql:host=localhost;dbname=your_database', 'db_user', 'db_password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if the search button is clicked
if (isset($_POST['search'])) {
  // Get the user input
  $keyword = $_POST['keyword'];
  $category = $_POST['category'];
  // Validate the user input
  if (empty($keyword) && empty($category)) {
    // Display an error message if both inputs are empty
    echo "<p>Please enter a keyword or select a category.</p>";
  } else {
    $request = array();
    $request['type'] = "search";
    $keyword= $_POST["search"];
    $request[] = $keyword;
    $response= $client->send_request($request);
        }      // Build the query based on the user input
       } //$sql = "SELECT * FROM flights WHERE type =$keyword "; //makes a query search where user searches on html form, php stores it in keyword and that what is "searched"
    //$params = [];
    //if (!empty($keyword)) {
      // Add a condition for the keyword
      //$sql .= "name LIKE :keyword OR description LIKE :keyword";
      //$params[':keyword'] = "%$keyword%";
    //}
    //if (!empty($category)) {
      // Add a condition for the category
      //if (!empty($keyword)) {
        // Add an AND operator if the keyword is not empty
        //$sql .= " AND ";
      //}
      //$sql .= "category = :category";
      //$params[':category'] = $category;
    //}
    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    // Fetch the results as an associative array
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Display the number of results
    //echo "<p>Found " . count($results) . " results.</p>";
  //}
//}        
?>