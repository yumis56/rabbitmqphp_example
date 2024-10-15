<?php
$DATABASE_HOST = '';
$DATABSE_USER = 'user';
$DATABASE_PASS = '12345';
$DATBASE_NAME = 'form';

$con = mysqli_connect($DATABASE_HOST, $DATABSE_USER, $DATABASE_PASS, $DATBASE_NAME);

if(mysqli_connect_error()) {
    echo('Connection Error. Try again Brah' . mysqli_connect_error());
}

if(!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    exit('Empty Field(s)');
}

if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])){
    exit('Values Empty');
}

if($stmt = $con->prepare('SELECT id, password FROM users WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        echo 'Username Already Exists. Please Try Again';
    } else {
        if($stmt = $con->prepare('INSERT INTO users(username, password, email) VALUES(?,?,?)')) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
            $stmt->execute();
            
            // Redirect to login.php after successful registration
            header('Location: login.php');
            exit(); // Important to stop further script execution
        } else {
            echo 'Error Occurred';
        }
    }
    $stmt->close();
} else {
    echo 'Error Occurred';
}
$con->close();
?>

