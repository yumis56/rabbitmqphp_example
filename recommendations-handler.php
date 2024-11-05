<?php
// recommendations_handler.php

session_start(); // Start the session at the beginning

// Example function to retrieve recommendations based on budget and space preferences
function getRecommendations($budget, $spacePreferences) {
    $recommendations = [];

    // Mock data - this could be retrieved from a database in a real application
    $spots = [
        ["name" => "Central Park", "type" => "Park", "budget" => "Cheap", "description" => "A large park in NYC", "image" => "testImages/centralpark.jpeg"],
        ["name" => "Fancy Restaurant", "type" => "Restaurant", "budget" => "Expensive", "description" => "Upscale dining experience", "image" => "testImages/fancyrestaurant.jpeg"],
        // Add more spots as needed
    ];

    // Filter spots based on selected preferences
    foreach ($spots as $spot) {
        if (($spot['budget'] === $budget) && in_array($spot['type'], $spacePreferences)) {
            $recommendations[] = $spot;
        }
    }

    return $recommendations;
}

// Check if form data was submitted and process it
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Use radio buttons and checkboxes from form submission
    $budget = $_POST['budgetOption'] ?? ''; // Single option for budget
    $spaces = $_POST['spaces'] ?? []; // Multiple options for spaces

    // Get recommendations based on form input
    $recommendations = getRecommendations($budget, $spaces);

    // Store recommendations data in session for use in recommendations.php
    $_SESSION['recommendations'] = $recommendations;
    
    // Redirect to recommendations.php to display the results
    header("Location: recommendations.php");
    exit();
}
?>
