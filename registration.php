<?php
session_start();
require_once 'connection.php';

// Kiểm tra dữ liệu đầu vào
if (!isset($_POST['username'], $_POST['email'], $_POST['password']) || 
    empty(trim($_POST['username'])) || 
    empty(trim($_POST['email'])) || 
    empty($_POST['password'])) {
    header('Location: register.php?error=Vui lòng điền đầy đủ thông tin!');
    exit();
}

$username = trim($_POST['username']);
$email    = trim(strtolower($_POST['email']));
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// BƯỚC 1: KIỂM TRA EMAIL CÓ PHẢI GMAIL KHÔNG
if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
    header('Location: register.php?error=Email phải có đuôi @gmail.com!');
    exit();
}

// BƯỚC 2: XÁC ĐỊNH QUYỀN (admin đầu tiên hay user thường)
$role = 'user';
if (isset($_POST['make_admin']) && $_POST['make_admin'] == 1) {
    $check = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    if ($check && $check->num_rows === 0) {
        $role = 'admin';
    }
}

// BƯỚC 3: KIỂM TRA TRÙNG USERNAME HOẶC EMAIL
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    header('Location: register.php?error=Tên đăng nhập hoặc email đã tồn tại!');
    exit();
}
$stmt->close();

// BƯỚC 4: ĐĂNG KÝ THÀNH CÔNG – LƯU VÀO CSDL
$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password, $role);

if ($stmt->execute()) {
    $stmt->close();
    $msg = $role === 'admin' 
        ? 'Tài khoản Admin đã được tạo thành công!' 
        : 'Đăng ký thành công! Hãy đăng nhập ngay.';
    
    header("Location: login.php?msg=" . urlencode($msg));
    exit();
} else {
    $stmt->close();
    header('Location: register.php?error=Đã có lỗi xảy ra, vui lòng thử lại!');
    exit();
}
?>