<!DOCTYPE html> 
<html lang="en">
    <head> 
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, intitial-scale=1.0">
        <title>Register</title>
        <link rel="stylesheet" type="text/css" href="style.css">  

    </head>
    <body>
        <div class="container">
            <h2>Create an Account</h2>

	    <?php if (isset($_SESSION['error'])): ?>
                <div class "error message" style="color: red;">
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                </div>
            <?php endif;?>




            <form method="POST" action="/rabbitmqphp_example/testRabbitMQClient.php">
                <div class="container">
                    <p>Please fill in all fields </p>
                </div>
		<div class="container darker">
		    <label for="email">Email:</label>
		    <input type="email" id="email" name="email"/> <br><br>
                    <label for="username">Username:</label>
		    <input type="text" id="username" name="username"/> <br><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password"/> <br><br>
                    <input type="submit" name="submit" value="Create Account"/>
                    
                </div>
	    </form>
		<br> <!--TODO remove this once css implemented !!-->
		<div>
		<a href="login.php">Log In</a>
		</div>
        </div>
    </body>
</html>
