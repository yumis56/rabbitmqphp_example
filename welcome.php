<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
session_start();

// Check if the user is logged in by verifying if the session variables are set
/* TODO session is not persisting, may be due to localhost or address issues
session_start();
if (!isset($_SESSION["user"]) || !isset($_SESSION["role"])) {
    // If the user is not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}



$username = $_SESSION["user"];
$role = $_SESSION["role"];
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body>
    <div class="welcome-container">
        <h1>Welcome<?php echo htmlspecialchars($username); ?>!</h1>
        <p>You have successfully logged in.</p>
       <!-- <p>Your role is: <?php echo htmlspecialchars($role); ?></p> -->
       <a href="search.php" class="button">Search</a>
       <a href="bookingHotel.php" class="button">Book a Hotel</a>
       <a href="bookingFlight.php" class="button">Book a Flight</a>
       <a href="rate-review.php" class="button">Rate&Review</a>
       <a href="/Apache/html/logout.php"class="button">Logout</a>
       
	
    </div>
</body>
</html>
