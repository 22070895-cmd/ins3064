<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

$product_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($product_id > 0) {
    $conn->query("DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");
}

header('Location: cart.php');
exit();
?>