<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit();
}

include "connection.php";

// Khi bấm nút SAVE
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO products (name, price, quantity) VALUES ('$name', '$price', '$quantity')";
    if ($conn->query($sql)) {
        header("Location: home.php");
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<html>
<head>
    <title>Create Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<div class="container mt-4">

    <h3>Create New Product</h3>

    <form method="POST">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Price:</label>
            <input type="number" name="price" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Quantity:</label>
            <input type="number" name="quantity" class="form-control" required>
        </div>

        <button type="submit" name="save" class="btn btn-success">Save</button>
        <a href="home.php" class="btn btn-secondary">Back</a>
    </form>

</div>
</body>
</html>
