<!DOCTYPE html>
<html>
<head>
    <title>Recommendations</title>
    <style>
        .card {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            margin: 10px;
            width: 300px;
        }

        .card:hover {
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }

        .container {
            padding: 2px 16px;
        }

        .recommendations-container {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <h2>Choose your preferred budget</h2>
    <form method="POST" action="recommendations_handler.php"> <!-- Update the action to point to the handler -->
        <input type="radio" id="cheap" name="budgetOption" value="Cheap">
        <label for="cheap">Cheaper Spots</label><br>
        <input type="radio" id="expensive" name="budgetOption" value="Expensive">
        <label for="expensive">Expensive Spots</label><br>

        <h2>Choose your preferred spaces</h2>
        <input type="checkbox" id="park" name="spaces[]" value="Park">
        <label for="park">Parks</label><br>
        <input type="checkbox" id="restaurant" name="spaces[]" value="Restaurant">
        <label for="restaurant">Restaurants</label><br>
        <input type="checkbox" id="shopping" name="spaces[]" value="Shopping Stores">
        <label for="shopping">Shopping Stores</label><br>
        <input type="checkbox" id="tourist" name="spaces[]" value="Tourist Spots">
        <label for="tourist">Tourist Spots</label><br>

        <button type="submit">Get Recommendations</button>
    </form>

    <br>
    <div>
        <header>Here are some spots we feel you'd like!</header>
        <div class="recommendations-container">
            <?php
            session_start(); // Start session to access recommendations

            // Retrieve recommendations from session
            $recommendations = $_SESSION['recommendations'] ?? [];

            // Check if there are recommendations to display
            if (!empty($recommendations)) {
                foreach ($recommendations as $rec) {
                    echo '<div class="card">';
                    echo '<img src="' . htmlspecialchars($rec['image']) . '" alt="Image of ' . htmlspecialchars($rec['name']) . '" style="width:100%">';
                    echo '<div class="container">';
                    echo '<h4><b>' . htmlspecialchars($rec['name']) . '</b></h4>';
                    echo '<p>' . htmlspecialchars($rec['description']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No recommendations available. Please select your preferences.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
