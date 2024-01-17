<?php
session_start(); // Start the session

// Unset  variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect back after logging out
header("Location: index.php");
exit();
?>