<?php
session_start();
session_reset();
echo("Successfully logged out. success");
header("location:login.php");
?>