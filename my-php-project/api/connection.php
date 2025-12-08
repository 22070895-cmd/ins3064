<?php
$host = "sql100.infinityfree.com"; 
$user = "if0_40575729";     
$pass = "Phanh207";  
$db   = "if0_40575729_group3";  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}
?>
