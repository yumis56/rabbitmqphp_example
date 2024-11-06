<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rate and Review</title>
</head>
<body>
    <?php  
    // Display Success or Error Message if able to display
    session_start(); // Start the session
    if (isset($_SESSION['message'])) {
        echo "<p>{$_SESSION['message']}</p>";
        unset($_SESSION['message']); // Clear the message after display 
    }
    ?>

    <form method="POST" action="rate-review-handler.php" enctype="multipart/form-data">
        <label for="guestName">Guest Name:</label><br>
        <input type="text" id="guestName" name="guestName" required><br><br>

        <label for="rating">Rate the Location (Between 1-5):</label><br>
        <input type="number" id="rating" name="rating" min="1" max="5" required><br><br>

        <label for="review">Review:</label><br>
        <textarea id="review" name="review" rows="10" cols="50" required></textarea><br><br>
        
        <label for="location">Location:</label><br>
        <input type="text" id="location" name="location" required><br><br>

        <label for="date">Date of Visit:</label><br>
        <input type="date" id="date" name="date" required><br><br>

        <label for="photo">Upload Photo (optional):</label><br>
        <input type="file" name="photo" id="photo" accept="image/*"><br><br>

        <button type="submit">Submit Review</button>
    </form>

    <br>
    <a href="book.php" class="button">Book a Flight</a>
    <a href="/Apache/html/logout.php">Logout</a>
</body>
</html>
