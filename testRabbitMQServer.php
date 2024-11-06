#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


function doLogin($username, $password) {
	//TODO this is for test, remove later
	
        //return array("returnCode" => '1', 'message' => "Login Success!");
	//


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

    try {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("SELECT password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username); // 's' specifies the variable type => 'string'
    
        // Execute the statement
        $stmt->execute();
        $stmt->store_result();
    
        // Check if the username exists
        if ($stmt->num_rows === 0) {
            return array("returnCode" => '0', 'message' => "Username does not exist!");
        }
    
        // Bind result to variable
        $stmt->bind_result($storedPassword);
        $stmt->fetch();
    
        // Check if the provided password matches the stored password
        if ($password === $storedPassword) {
            return array("returnCode" => '1', 'message' => "Login success!");
        } else {
            return array("returnCode" => '0', 'message' => "Incorrect username or password!");
        }
        
    } finally {
        //close afterwards or else you're causing a DoS, no good!
        if ($stmt) {
            $stmt->close();
        }
        $conn->close();
    }
} //end of doLogin



function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'], $request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

