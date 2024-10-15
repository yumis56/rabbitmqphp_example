<?php
require(__DIR__."/MQPublish.inc.php");
session_start();?>
    <?php
    if (isset($_POST["submit"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        //TODO validate
        if (empty($username) || empty($password)) {
            echo '<p class="error">Please fill in all fields.</p>';
        } else {
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                //$response = login($username, $password);
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
