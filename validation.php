<?php
session_start();

/*----------------------------------------------------
  1. KẾT NỐI DATABASE
----------------------------------------------------*/
$con = mysqli_connect('localhost', 'root', '', 'login_1');
if (!$con) {
    die('Connection failed: ' . mysqli_connect_error());
}

/*----------------------------------------------------
  2. NHẬN DỮ LIỆU TỪ FORM
----------------------------------------------------*/
if (!isset($_POST['user'], $_POST['password'])) {
    header('location:login.php');
    exit();
}

$name = mysqli_real_escape_string($con, $_POST['user']);
$pass = mysqli_real_escape_string($con, $_POST['password']);
$remember = isset($_POST['remember']); // checkbox remember

/*----------------------------------------------------
  3. CHECK USER TRONG DATABASE
----------------------------------------------------*/
$sql = "SELECT * FROM table3 WHERE username='$name' AND password='$pass'";
$result = mysqli_query($con, $sql);
$num = mysqli_num_rows($result);

/*----------------------------------------------------
  4. ĐĂNG NHẬP THÀNH CÔNG
----------------------------------------------------*/
if ($num == 1) {

    // lưu session
    $_SESSION['username'] = $name;

    // Nếu chọn Remember me -> set cookie 30 ngày
    if ($remember) {
        setcookie("username", $name, time() + (86400 * 30), "/");
        setcookie("password", $pass, time() + (86400 * 30), "/");
    } else {
        // Nếu KHÔNG chọn thì xóa cookie
        setcookie("username", "", time() - 3600, "/");
        setcookie("password", "", time() - 3600, "/");
    }

    header('location:home.php');
    exit();
}

/*----------------------------------------------------
  5. ĐĂNG NHẬP THẤT BẠI
----------------------------------------------------*/
else {
    header('location:login.php?error=1');
    exit();
}
?>
