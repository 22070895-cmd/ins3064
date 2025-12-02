<?php
require_once 'auth.php';
require_role('admin');
require_once 'connection.php';

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'], $_POST['status'])) {
        $orderId = (int)$_POST['order_id'];
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        $message = "Cập nhật trạng thái thành công.";
    }
    if (isset($_POST['user_id'], $_POST['role'])) {
        $userId = (int)$_POST['user_id'];
        $role = $_POST['role'];
        if ($userId !== $_SESSION['user_id']) {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $role, $userId);
            $stmt->execute();
            $message = "Cập nhật quyền thành công.";
        }
    }
}

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$orders = $conn->query("SELECT o.*, u.username, p.name AS product_name FROM orders o JOIN users u ON o.user_id=u.id JOIN products p ON o.product_id=p.id ORDER BY o.created_at DESC");
$users = $conn->query("SELECT id, username, role FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f9fc;
            color: #333;
            min-height: 100vh;
        }
        .navbar {
            background: #1a1a1a !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.8rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .stat-icon {
            font-size: 2.8rem;
            margin-bottom: 0.8rem;
        }
        .table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #eee;
        }
        .badge {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4 fw-bold" href="#">
            Admin Panel
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="products.php" class="btn btn-outline-light btn-sm">Xem trang User</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Đăng xuất</a>
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

    <div class="row g-4 mb-5">
        <div class="col-md-3"><div class="stat-card"><span class="material-icons stat-icon text-primary">shopping_bag</span><h3><?php echo $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0]; ?></h3><p>Đơn hàng</p></div></div>
        <div class="col-md-3"><div class="stat-card"><span class="material-icons stat-icon text-success">inventory</span><h3><?php echo $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0]; ?></h3><p>Sản phẩm</p></div></div>
        <div class="col-md-3"><div class="stat-card"><span class="material-icons stat-icon text-info">people</span><h3><?php echo $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0]; ?></h3><p>Người dùng</p></div></div>
        <div class="col-md-3"><div class="stat-card"><span class="material-icons stat-icon text-warning">shield</span><h3><?php echo $conn->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0]; ?></h3><p>Admin</p></div></div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Quản lý đơn hàng</span>
                    <a href="product_add.php" class="btn btn-light btn-sm">+ Thêm sản phẩm</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Khách</th><th>Sản phẩm</th><th>Số lượng</th><th>Trạng thái</th><th>Hành động</th></tr>
                        </thead>
                        <tbody>
                            <?php while($o = $orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($o['username']); ?></td>
                                <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                                <td><?php echo $o['quantity']; ?></td>
                                <td><span class="badge bg-<?php echo $o['status']=='pending'?'warning':($o['status']=='processing'?'info':($o['status']=='shipped'?'success':'danger')); ?>"><?php echo ucfirst($o['status']); ?></span></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $o['status']=='pending'?'selected':''; ?>>Pending</option>
                                            <option value="processing" <?php echo $o['status']=='processing'?'selected':''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $o['status']=='shipped'?'selected':''; ?>>Shipped</option>
                                            <option value="cancelled" <?php echo $o['status']=='cancelled'?'selected':''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Danh sách sản phẩm</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <?php while($p = $products->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($p['image_url']); ?>" class="product-img me-3" 
                                     onerror="this.src='https://via.placeholder.com/50?text=?'" alt="">
                                <?php echo htmlspecialchars($p['name']); ?>
                            </td>
                            <td><?php echo number_format($p['price']); ?>đ</td>
                            <td><?php echo $p['quantity']; ?></td>
                            <td>
                                <a href="product_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="product_delete.php?id=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')">Xóa</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">Quản lý người dùng</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <?php while($u = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><span class="badge bg-<?php echo $u['role']=='admin'?'danger':'secondary'; ?>"><?php echo $u['role']; ?></span></td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="user" <?php echo $u['role']=='user'?'selected':''; ?>>User</option>
                                        <option value="admin" <?php echo $u['role']=='admin'?'selected':''; ?>>Admin</option>
                                    </select>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>