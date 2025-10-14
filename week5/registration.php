<?php
session_start();

/* Kết nối tới database */
$con = mysqli_connect('localhost', 'root', '', 'login_1');
if (!$con) {
    die('Connection failed: ' . mysqli_connect_error());
}

/* Lấy dữ liệu từ form đăng ký */
$name = $_POST['user'];
$pass = $_POST['password'];

/* Kiểm tra xem username đã tồn tại chưa */
$check = "SELECT * FROM table3 WHERE username='$name'";
$result = mysqli_query($con, $check);
$num = mysqli_num_rows($result);

if ($num == 1) {
    // Nếu username đã tồn tại
    echo "<script>alert('Username already exists! Please choose another.'); window.location='login.php';</script>";
} else {
    // Nếu chưa tồn tại thì lưu user mới
    $reg = "INSERT INTO table3 (username, password) VALUES ('$name', '$pass')";
    mysqli_query($con, $reg);
    echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
}

mysqli_close($con);
?>
