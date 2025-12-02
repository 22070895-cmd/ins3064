<?php
session_start();
require_once 'connection.php';

if (!isset($_POST['user'], $_POST['password'])) {
    header('Location: register.php');
    exit();
}

$username = trim($_POST['user']);
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Kiểm tra xem có được phép tạo admin không
$role = 'user';
if (isset($_POST['make_admin']) && $_POST['make_admin'] == 1) {
    // Chỉ cho phép nếu chưa có admin nào
    $check = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    if ($check->num_rows === 0) {
        $role = 'admin';
    }
}

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Username already exists! Please choose another.'); window.location='register.php';</script>";
    exit();
}
$stmt->close();

$insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$insert->bind_param("sss", $username, $password, $role);
$insert->execute();
$insert->close();

$msg = $role === 'admin' ? 'Tài khoản Admin đã được tạo thành công!' : 'Registration successful!';
echo "<script>alert('$msg'); window.location='login.php';</script>";
exit();