<?php
session_start();
require 'config.php';


$_SESSION = array();
session_unset();


session_destroy();


session_regenerate_id(true);


session_start();
$_SESSION['logout_message'] = "You have successfully logged out!";


header("Location: index.php");
exit();
?>
