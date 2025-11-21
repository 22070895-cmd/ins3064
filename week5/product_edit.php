<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit();
}

include "connection.php";

// Lấy ID trên URL
$id = $_GET['id'];

// Lấy dữ liệu hiện tại
$sql = "SELECT * FROM products WHERE id=$id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

// Khi bấm UPDATE
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $sqlUpdate = "UPDATE products SET name='$name', price='$price', quantity='$quantity' WHERE id=$id";

    if ($conn->query($sqlUpdate)) {
        header("Location: home.php");
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<div class="container mt-4">

    <h3>Edit Product</h3>

    <form method="POST">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
        </div>

        <div class="form-group">
            <label>Price:</label>
            <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="form-group">
            <label>Quantity:</label>
            <input type="number" name="quantity" class="form-control" value="<?php echo $product['quantity']; ?>" required>
        </div>

        <button type="submit" name="update" class="btn btn-warning">Update</button>
        <a href="home.php" class="btn btn-secondary">Back</a>
    </form>

</div>
</body>
</html>
