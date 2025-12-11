<?php
$host = "sql100.infinityfree.com"; 
$user = "if0_40633149";      
$pass = "Phanh20704";  
$db   = "if0_40633149_final";  

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}
?>
