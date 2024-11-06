#!/usr/bin/php
<?php
//TODO: this file is so that testRabbitMQServer.php does not get crowded. use require_once to integrate into the server file.
//NOTE: this file needs your .env file to be set up correctly. DO NOT PUSH SENSITIVE DATA TO GITHUB.

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');
$dbName = getenv('DB_NAME');

 $conn = mysqli_connect($host, $dbUser, $dbPass, $dbName);

if ($conn->connect_error){
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
	if ($stmt){
		$stmt->close();
	}
	$conn->close();
}

?>
