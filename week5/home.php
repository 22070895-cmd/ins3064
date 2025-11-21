<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit();
}

include "connection.php"; // file kết nối DB

// Lấy danh sách sản phẩm
$sql = "SELECT * FROM products"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background: url('image2.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        /* căn giữa khối nội dung */
        .page {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 50px;
            width: 100%;
            height: 100%;
        }

        /* hộp nội dung */
        .content-box {
            width: 900px;
            background: rgba(250, 249, 249, 0.85);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(219, 199, 199, 0.15);
        }

        table {
            width: 100%;
            border-collapse: collapse !important;
        }

        th, td {
            text-align: center;
            vertical-align: middle !important;
            padding: 12px !important;
        }
        /* Tiêu đề bảng */
        th {
            background: #fef9f9ff;
            font-weight: bold;
        }

        /* Cố định chiều rộng & căn giữa cột Action */
        .action-col {
            width: 160px;
            text-align: center;
            vertical-align: middle !important;
        }

        /* Căn nút Edit/Delete thẳng hàng */
        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .float-right {
            float: right;
            text-decoration: none;
            color: #cc2e2eff;
        }

        .float-right:hover {
            text-decoration: underline;
       
}

    </style>
</head>

<body>
<div class="page">
    <div class="content-box">
        <a class="float-right" href="logout.php">LOGOUT</a>
        <h2>Welcome <?php echo $_SESSION['username']; ?></h2>

        <h3 class="mt-4">Product List</h3>

        <!-- NÚT CREATE -->
     <a href="product_add.php" class="btn btn-success mb-3" style="background: yellow; color: black; border: none;">Create Product</a>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="35%">Name</th>
                    <th width="20%">Quantity</th>
                    <th width="20%">Price</th>
                    <th width="25%">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['price']}</td>
                        
                        <td class='action-col'>
                            <div class='action-buttons'>
                                <a href='product_edit.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='product_delete.php?id={$row['id']}' 
                                   onclick=\"return confirm('Bạn có chắc chắn muốn xóa?');\" 
                                   class='btn btn-danger btn-sm'>Delete</a>
                            </div>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>Không có sản phẩm nào</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
