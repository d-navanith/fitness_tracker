<?php
session_start();
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    header('Location: login.php');
    exit();
}
?>