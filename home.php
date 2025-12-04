<?php
require_once 'auth.php';
require_role('admin');
require_once 'connection.php';

$message = null;

// Xử lý cập nhật trạng thái đơn hàng
if ($_POST['order_id'] ?? false) {
    $oid = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowed = ['pending','paid','processing','shipped','cancelled'];
    if (in_array($status, $allowed)) {
        $conn->query("UPDATE orders SET status = '$status' WHERE id = $oid");
        $message = "Cập nhật trạng thái thành công!";
    }
}

$orders = $conn->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý đơn hàng</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
    background: linear-gradient(rgba(0,0,0,0.05), rgba(0,0,0,0.05)), url('white.jpg') center/cover no-repeat fixed;
}
        .navbar { background: #1a1a1a !important; }
        .card { border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 2rem; }
        .card-header { background: #0d6efd; color: white; font-weight: 600; }
        .card-header-green { background: #198754 !important; }
        .table th { background: #f1f3f5; text-align: center; vertical-align: middle !important; }
        .table td { vertical-align: middle; text-align: center; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; }
        .badge { font-size: 0.9rem; padding: 0.5em 1em; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top shadow">
    <div class="container-fluid">
        <a class="navbar-brand fs-4 fw-bold" href="#">Admin Panel</a>
        <div class="d-flex gap-3">
            <span class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="products.php" class="btn btn-outline-light">Xem trang khách</a>
            <a href="logout.php" class="btn btn-outline-danger">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- BẢNG QUẢN LÝ ĐƠN HÀNG -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Quản lý đơn hàng (<?php echo $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0]; ?> đơn)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($o = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><strong class="text-primary"><?php echo $o['order_code']; ?></strong></td>
                        <td><?php echo htmlspecialchars($o['username']); ?></td>
                        <td><strong><?php echo number_format($o['total_amount']); ?>đ</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                        <td>
                            <span class="badge bg-<?php
                                echo $o['status']=='paid'?'success':($o['status']=='pending'?'warning':($o['status']=='processing'?'info':($o['status']=='shipped'?'primary':'danger')));
                            ?>">
                                <?php echo ucfirst($o['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Pending</option>
                                    <option value="paid" <?php echo $o['status']=='paid'?'selected':''; ?>>Paid</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- BẢNG DANH SÁCH SẢN PHẨM -->
    <div class="card">
        <div class="card-header card-header-green d-flex justify-content-between align-items-center">
            <span>Danh sách sản phẩm</span>
            <a href="product_add.php" class="btn btn-light btn-sm fw-bold">+ Thêm sản phẩm</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Kho</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = $products->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                                 class="product-img" 
                                 onerror="this.src='https://via.placeholder.com/60?text=?'">
                        </td>
                        <td class="text-start"><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo number_format($p['price']); ?>đ</td>
                        <td><?php echo $p['quantity']; ?></td>
                        <td>
                            <a href="product_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="product_delete.php?id=<?php echo $p['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>