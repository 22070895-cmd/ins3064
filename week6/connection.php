<?php
$host = "localhost";   // hoặc 127.0.0.1
$user = "root";        // tài khoản mặc định của XAMPP hoặc Laragon
$pass = "";            // mật khẩu, để trống nếu dùng XAMPP mặc định
$db   = "userdb";      // tên database vừa tạo

$link = mysqli_connect($host, $user, $pass, $db);

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}
?>


