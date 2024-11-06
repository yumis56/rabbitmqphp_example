<?php
session_start(); //you NEED to use the session to destroy the session

session_unset();
session_destroy();
session_start();

header('Location: login.php');
exit();
?>
