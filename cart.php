<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

$user_id = $_SESSION['user_id'];

// Xử lý cập nhật số lượng + xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['qty'] as $product_id => $qty) {
            $qty = (int)$qty;
            $product_id = (int)$product_id;
            if ($qty <= 0) {
                $conn->query("DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");
            } else {
                // Kiểm tra tồn kho
                $res = $conn->query("SELECT quantity FROM products WHERE id = $product_id");
                $stock = $res->fetch_row()[0];
                if ($qty > $stock) $qty = $stock;
                $conn->query("UPDATE cart SET quantity = $qty WHERE user_id = $user_id AND product_id = $product_id");
            }
        }
    }
}

// Lấy dữ liệu giỏ hàng
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
    <title>Giỏ hàng - Cửa hàng Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #f0f2f5, #ffffff);
            min-height: 100vh;
        }
        .navbar {
            background: #0d6efd !important;
            box-shadow: 0 4px 20px rgba(13,110,253,0.25);
        }
        .cart-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .cart-item {
            transition: all 0.3s;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }
        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .product-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
        }
        .quantity-input {
            width: 80px;
            text-align: center;
            font-weight: 600;
        }
        .total-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #e91e63;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            font-size: 1.3rem;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(238, 90, 82, 0.4);
        }
        .btn-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(238, 90, 82, 0.5);
        }
        .btn-remove {
            color: #e74c3c;
            transition: all 0.3s;
        }
        .btn-remove:hover {
            color: #c0392b;
            transform: scale(1.2);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-3 fw-bold" href="products.php">Cửa hàng</a>
        <div class="d-flex gap-3 align-items-center">
            <a href="products.php" class="btn btn-outline-light">Tiếp tục mua</a>
            <a href="logout.php" class="btn btn-outline-light">Đăng xuất</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <!-- Header giỏ hàng -->
    <div class="cart-header mb-4">
        <h1 class="display-5 fw-bold mb-3">Giỏ hàng của bạn</h1>
        <p class="fs-5 opacity-90">Bạn đang có <strong><?php echo $cart_items->num_rows; ?></strong> sản phẩm</p>
    </div>

    <?php if ($cart_items->num_rows == 0): ?>
        <div class="text-center py-5">
            <span class="material-icons" style="font-size: 120px; color: #ddd;">shopping_cart_off</span>
            <h3 class="mt-4 text-muted">Giỏ hàng trống</h3>
            <a href="products.php" class="btn btn-primary btn-lg mt-3">Bắt đầu mua sắm</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="update_cart" value="1">
            
            <?php while ($item = $cart_items->fetch_assoc()): 
                $subtotal = $item['price'] * $item['quantity'];
                $total_price += $subtotal;
            ?>
                <div class="cart-item bg-white">
                    <div class="row g-0 align-items-center p-4">
                        <!-- Ảnh sản phẩm -->
                        <div class="col-12 col-md-2 text-center mb-3 mb-md-0">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 class="product-img" 
                                 onerror="this.src='https://via.placeholder.com/120?text=No+Image'">
                        </div>

                        <!-- Thông tin sản phẩm -->
                        <div class="col-12 col-md-5">
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="text-muted mb-0">Đơn giá: <?php echo number_format($item['price']); ?>đ</p>
                        </div>

                        <!-- Số lượng -->
                        <div class="col-12 col-md-3 text-center">
                            <div class="input-group justify-content-center">
                                <button type="button" class="btn btn-outline-secondary" onclick="updateQty(<?php echo $item['product_id']; ?>, -1)">–</button>
                                <input type="number" 
                                       name="qty[<?php echo $item['product_id']; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="<?php echo $item['stock']; ?>"
                                       class="form-control quantity-input mx-2" 
                                       id="qty-<?php echo $item['product_id']; ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="updateQty(<?php echo $item['product_id']; ?>, 1)">+</button>
                            </div>
                            <small class="text-muted">Tồn kho: <?php echo $item['stock']; ?></small>
                        </div>

                        <!-- Thành tiền + Xóa -->
                        <div class="col-12 col-md-2 text-md-end">
                            <div class="fs-5 fw-bold text-danger mb-3">
                                <?php echo number_format($subtotal); ?>đ
                            </div>
                            <a href="cart_remove.php?id=<?php echo $item['product_id']; ?>" class="btn btn-remove">
                                <span class="material-icons">delete</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Tổng tiền & Thanh toán -->
            <div class="bg-white rounded-4 shadow-lg p-4 mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Tổng cộng:</h4>
                        <p class="text-muted mb-0">Đã bao gồm VAT (nếu có)</p>
                    </div>
                    <div class="text-end">
                        <div class="total-price"><?php echo number_format($total_price); ?>đ</div>
                        <button type="submit" class="btn btn-warning me-3">Cập nhật giỏ</button>
                        <a href="checkout.php" class="btn btn-checkout">
                            Thanh toán ngay
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
// Tăng giảm số lượng bằng nút + -
function updateQty(productId, change) {
    const input = document.getElementById('qty-' + productId);
    let newVal = parseInt(input.value) + change;
    if (newVal < 1) newVal = 1;
    if (newVal > parseInt(input.max)) newVal = input.max;
    input.value = newVal;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>