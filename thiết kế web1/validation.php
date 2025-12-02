<?php
session_start();
require_once 'connection.php';

if (!isset($_POST['user'], $_POST['password'])) {
    header('Location: login.php?error=Thiếu thông tin đăng nhập');
    exit();
}

$name = trim($_POST['user']);
$pass = $_POST['password'];
$remember = isset($_POST['remember']);

$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($pass, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($remember) {
        setcookie("username", $user['username'], time() + (86400 * 30), "/");
    } else {
        setcookie("username", "", time() - 3600, "/");
    }

    if ($user['role'] === 'admin') {
        header('Location: home.php');
    } else {
        header('Location: products.php');
    }
    exit();
}

header('Location: login.php?error=Sai tài khoản hoặc mật khẩu');
exit();
