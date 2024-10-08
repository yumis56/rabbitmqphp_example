<?php
require(__DIR__."/MQPublish.inc.php");
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form method="post">
	<div class="container">
  <p>Please fill in all fileds</p>
</div>
	<div class="container darker">
        <label for="username">Email:</label>
        <input type="text" id="username" name="username"/> <br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password"/> <br><br>
        <input type="submit" name="submit" value="Login"/>
        <a href="register.php">Register</a>

<div class="container">  
  </form>

    <?php
    if (isset($_POST["submit"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        //TODO validate
        if (empty($username) || empty($password)) {
            echo '<p class="error">Please fill in all fields.</p>';
        } else {
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $response = login($username, $password);
            } else {
                echo '<p class="error">Something went wrong. Please ensure you entered a valid email and that all fields are filled.</p>';
            }
        }

        //calls function from MQPublish.inc.php to communicate with MQ
        if (isset($response) && $response["status"] == 200) {
            $_SESSION["user"] = $response["data"];
            $_SESSION["role"] = $response["role"];
            header("location:welcome.php");
        } else {
            if (isset($response)) {
                var_export($response);
            }
        }
    }
    ?>
</div>
</body>
</html>
