<?php
// Start the session
require 'database.php'; 
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear all cookies by setting their expiration time to the past
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 3600, '/'); // Set cookie expiration time to past
    }
}

// Redirect to login.php
header("Location: login.php");
exit();
?>
