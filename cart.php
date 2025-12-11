<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

$user_id = $_SESSION['user_id'];

// XỬ LÝ CẬP NHẬT + XÓA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $product_id => $qty) {
            $qty = (int)$qty;
            $product_id = (int)$product_id;
            if ($qty <= 0) {
                $conn->query("DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");
            } else {
                $res = $conn->query("SELECT quantity FROM products WHERE id = $product_id");
                $stock = $res->fetch_row()[0] ?? 0;
                if ($qty > $stock) $qty = $stock;
                $conn->query("UPDATE cart SET quantity = $qty WHERE user_id = $user_id AND product_id = $product_id");
            }
        }
    }
}

// LẤY DỮ LIỆU GIỎ HÀNG
$cart_items = $conn->query("
    SELECT c.*, p.name, p.price, p.image_url, p.quantity as stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = $user_id
    ORDER BY c.created_at DESC
");

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Apple Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.05), rgba(0,0,0,0.05)), url('assets/white.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
        }
        .navbar {
            background: #2a2c2dff !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .cart-container {
            max-width: 1100px;
            margin: 2rem auto;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .cart-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        .cart-header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .cart-body {
            padding: 2rem;
        }
        .cart-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #eee;
            border-radius: 18px;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
            background: #fdfdfd;
        }
        .cart-item:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        .product-img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 16px;
            border: 3px solid #f0f0f0;
        }
        .product-info h5 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1a1a1a;
        }
        .price-single {
            color: #666;
            font-size: 1rem;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quantity-controls button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #0d6efd;
            background: white;
            color: #0d6efd;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .quantity-controls input {
            width: 70px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 12px;
            padding: 0.5rem;
            font-weight: bold;
        }
        .subtotal {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e91e63;
        }
        .remove-btn {
            color: #e74c3c;
            font-size: 1.8rem;
            transition: all 0.3s;
        }
        .remove-btn:hover {
            color: #c0392b;
            transform: scale(1.2);
        }
        .cart-summary {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
        }
        .total-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #e91e63;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            font-size: 1.5rem;
            padding: 1.2rem 4rem;
            border-radius: 50px;
            font-weight: 700;
            box-shadow: 0 15px 40px rgba(238,90,82,0.4);
            transition: all 0.4s;
        }
        .btn-checkout:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(238,90,82,0.5);
        }
        .empty-cart {
            text-align: center;
            padding: 5rem 2rem;
        }
        .empty-cart .material-icons {
            font-size: 120px;
            color: #ddd;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-3 fw-bold" href="products.php">Apple Online</a>
        <div class="d-flex gap-3 align-items-center">
            <a href="products.php" class="btn btn-outline-light btn-lg">Tiếp tục mua sắm</a>
            <a href="logout.php" class="btn btn-outline-light">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="cart-container">
        <div class="cart-header">
            <h1>Giỏ hàng của bạn</h1>
            <p class="fs-4 opacity-90">Bạn đang có <strong><?= $cart_items->num_rows ?></strong> sản phẩm trong giỏ</p>
        </div>

        <?php if ($cart_items->num_rows == 0): ?>
            <div class="empty-cart">
                <span class="material-icons">shopping_cart_off</span>
                <h2 class="mt-4 text-muted">Giỏ hàng trống</h2>
                <a href="products.php" class="btn btn-primary btn-lg mt-4">Bắt đầu mua sắm ngay!</a>
            </div>
        <?php else: ?>
            <form method="POST" class="cart-body">
                <input type="hidden" name="update_cart" value="1">
                
                <?php while ($item = $cart_items->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total_price += $subtotal;
                ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                             class="product-img" 
                             onerror="this.src='https://via.placeholder.com/130?text=No+Image'">

                        <div class="product-info flex-grow-1">
                            <h5><?= htmlspecialchars($item['name']) ?></h5>
                            <p class="price-single">Giá: <?= number_format($item['price']) ?>đ</p>
                        </div>

                        <div class="quantity-controls">
                            <button type="button" onclick="updateQty(<?= $item['product_id'] ?>, -1)">−</button>
                            <input type="number" 
                                   name="qty[<?= $item['product_id'] ?>]" 
                                   value="<?= $item['quantity'] ?>" 
                                   min="1" max="<?= $item['stock'] ?>" 
                                   id="qty-<?= $item['product_id'] ?>">
                            <button type="button" onclick="updateQty(<?= $item['product_id'] ?>, 1)">+</button>
                        </div>

                        <div class="text-end me-3">
                            <div class="subtotal"><?= number_format($subtotal) ?>đ</div>
                        </div>

                        <a href="cart_remove.php?id=<?= $item['product_id'] ?>" class="remove-btn">
                            <span class="material-icons">delete_forever</span>
                        </a>
                    </div>
                <?php endwhile; ?>

                <div class="cart-summary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">Tổng thanh toán:</h3>
                            <p class="text-muted mb-0">(Đã bao gồm VAT nếu có)</p>
                        </div>
                        <div class="text-end">
                            <h2 class="total-price"><?= number_format($total_price) ?>đ</h2>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-warning btn-lg me-4">
                            Cập nhật giỏ hàng
                        </button>
                        <a href="checkout.php" class="btn btn-checkout">
                            Thanh toán ngay
                        </a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQty(productId, change) {
    const input = document.getElementById('qty-' + productId);
    let val = parseInt(input.value) + change;
    if (val < 1) val = 1;
    if (val > parseInt(input.max)) val = input.max;
    input.value = val;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
