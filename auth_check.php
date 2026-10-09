<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Accepts any active login session variable
$is_logged_in = isset($_SESSION['user_email']) || 
               isset($_SESSION['email']) || 
               isset($_SESSION['username']) || 
               isset($_SESSION['user_id']) || 
               isset($_SESSION['full_name']);

if (!$is_logged_in) {
    header("Location: login.php");
    exit();
}
?>