<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Hotel</title>
</head>
<body>
    <?php 
    // Display Success or Error Message if present
    if (isset($_SESSION['message'])){
        echo "<p>{$_SESSION['message']}</p>";
        unset($_SESSION['message']); // Clear the message after display 
    }
    ?>

    <form action="booking-handler.php" method="post">
        <label for="guestName">Guestname/label> <br> 
        <input type="text" name="guestName" required> <br> 
        
        <label for="numGuest">How many people in total?</label><br> 
        <input type="number" name="numGuest" min="1" required> <br> 
        
        <label for="checkinDate">Expected check-in date?</label><br> 
        <input type="date" name="checkinDate" required> <br> 
        
        <label for="checkinTime">Expected check-in time?</label><br> 
        <input type="time" name="checkinTime" required> <br> 
        
        <label for="checkoutDate">Expected check-out date</label><br> 
        <input type="date" name="checkoutDate" required> <br> 
        
        <label for="checkoutTime">Expected check-out time</label><br> 
        <input type="time" name="checkoutTime" required> <br> 
        
        <label for="hotelName">Which hotel will you be booking?</label><br> 
        <input type="text" name="hotelName" required> <br> 
        
        <input type="submit" value="Submit Booking">
    </form>
</body>
</html>
