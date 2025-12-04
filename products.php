<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$message = $_GET['message'] ?? null;
$error = $_GET['error'] ?? null;
$user = current_user();

// Đếm số lượng trong giỏ hàng để hiện badge
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $res = $conn->query("SELECT SUM(quantity) FROM cart WHERE user_id = " . (int)$_SESSION['user_id']);
    $cart_count = $res->fetch_row()[0] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apple Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url('white.jpg') center/cover no-repeat fixed;; min-height: 100vh; }
        .navbar { background: #2a2c2dff !important; box-shadow: 0 4px 15px rgba(13,110,253,0.3); }
        .card-product {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .card-product:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .card-product img {
            height: 230px;
            object-fit: cover;
        }
        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e91e63;
        }
        .btn-add-cart {
            background: #ff9800;
            color: white;
            border-radius: 12px;
            font-weight: 600;
        }
        .btn-add-cart:hover {
            background: #e68900;
            color: white;
        }
        .badge-cart {
            font-size: 0.7rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4 fw-bold" href="products.php">
            Apple Online
        </a>
        <div class="d-flex align-items-center gap-3">
            <?php if (is_admin()): ?>
                <a href="home.php" class="btn btn-light btn-sm">Admin</a>
            <?php endif; ?>

            <a href="cart.php" class="btn btn-outline-light position-relative">
                Giỏ hàng
                <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-cart">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>

            <span class="text-white"><?php echo htmlspecialchars($user['username']); ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Đăng Xuất</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <h2 class="text-center mb-5 fw-bold text-dark">Danh sách sản phẩm</h2>

    <?php if ($products->num_rows == 0): ?>
        <div class="text-center py-5">
            <span class="material-icons" style="font-size: 100px; color: #ddd;">inventory_2</span>
            <p class="mt-3 fs-4 text-muted">Không có sản phẩm nào</p>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php while ($p = $products->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 card-product">
                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($p['name']); ?>"
                             onerror="this.src='https://via.placeholder.com/400x300.png?text=No+Image'">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
                            <p class="price mt-2"><?php echo number_format($p['price'], 0, ',', '.'); ?> đ</p>
                            <p class="text-muted mb-3">Còn lại: <strong><?php echo $p['quantity']; ?></strong></p>

                            <?php if ($p['quantity'] > 0): ?>
                                <form action="cart_add.php" method="POST" class="mt-auto">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <div class="input-group">
                                        <input type="number" name="quantity" 
                                               min="1" max="<?php echo $p['quantity']; ?>" 
                                               value="1" 
                                               class="form-control" 
                                               style="max-width: 90px;">
                                        <button type="submit" class="btn btn-add-cart">
                                            Thêm vào giỏ
                                        </button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="text-danger text-center fw-bold">Hết hàng</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>