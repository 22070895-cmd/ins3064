<?php
session_start();
require_once 'connection.php';

if ($_POST['login'] && $_POST['password']) {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // Kiểm tra login là email hay username
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    }
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: home.php");
        } else {
            header("Location: products.php");
        }
        exit();
    } else {
        header("Location: login.php?error=Sai+tên+đăng+nhập+hoặc+mật+khẩu");
        exit();
    }
} else {
    header("Location: login.php?error=Vui+lòng+nhập+đầy+đủ");
    exit();
}
?>