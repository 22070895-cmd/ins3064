<?php
include("db_connect.php");
 
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
 
    $check = mysqli_query($link, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        echo "Existed";
    } else {
        
        $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if (mysqli_query($link, $query)) {
            echo "Successful";
        } else {
            echo "Error " . mysqli_error($link);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Register</h2>
    <form action="" method="post">
        <label for="">User name</label>
        <input type="text" name="username" require> <br><br>
        <label for="">Password</label>
        <input type="password" name="password" id="" require> <br><br>

        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>