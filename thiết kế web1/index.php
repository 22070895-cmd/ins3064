<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: home.php');
        exit();
    }
    header('Location: products.php');
    exit();
}
header('Location: login.php');
exit;