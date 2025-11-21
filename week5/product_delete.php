<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit();
}

include "connection.php";

$id = $_GET['id'];

// Xóa sản phẩm
$sql = "DELETE FROM products WHERE id=$id";

if ($conn->query($sql)) {
    header("Location: home.php");
    exit();
} else {
    echo "Lỗi: " . $conn->error;
}
?>
