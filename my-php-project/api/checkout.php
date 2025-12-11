<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

$user_id = $_SESSION['user_id'];

// Lấy giỏ hàng + thông tin sản phẩm
$cart = $conn->query("
    SELECT c.*, p.name, p.price, p.image_url, p.quantity as stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = $user_id
");

if ($cart->num_rows == 0) {
    header('Location: cart.php');
    exit();
}

// Tính tổng tiền
$total = 0;
$items = [];
while ($row = $cart->fetch_assoc()) {
    $total += $row['price'] * $row['quantity'];
    $items[] = $row;
}

// TẠO MÃ ĐƠN HÀNG ĐẸP: HD-0001, HD-0002...
$count = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0] + 1;
$order_code = 'HD-' . str_pad($count, 4, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán QR - Cửa hàng Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .checkout-card {
            max-width: 1000px;
            margin: auto;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0,0,0,0.4);
            background: white;
        }
        .qr-section {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 3rem 2rem;
            text-align: center;
            color: white;
        }
        .qr-img {
            width: 300px;
            height: 300px;
            border: 16px solid white;
            border-radius: 24px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            background: white;
        }
        .order-section {
            background: white;
            color: #333;
            padding: 2.5rem;
        }
        .product-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .product-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 14px;
        }
        .total-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #e91e63;
        }
        .order-code {
            background: #fff3cd;
            color: #d97706;
            padding: 1rem 2.5rem;
            border-radius: 16px;
            font-size: 1.5rem;
            font-weight: bold;
            display: inline-block;
            margin: 1.5rem 0;
        }
        .btn-pay {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            font-size: 1.4rem;
            padding: 1.2rem 4rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(238,90,82,0.4);
        }
        .btn-pay:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(238,90,82,0.5);
        }
        .countdown {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6b6b;
        }
    </style>
</head>
<body>

<div class="checkout-card">
    <div class="row g-0">
        <!-- CỘT TRÁI: MÃ QR -->
        <div class="col-lg-5 qr-section">
            <h2 class="mb-4">
                Quét mã QR để thanh toán
            </h2>
            
            <!-- Ảnh QR của bạn -->
            <img src="assets/qr.jpg" alt="QR thanh toán" class="qr-img img-fluid mb-4">
            <!-- Đổi tên file nếu cần: qr.jpg, myqr.png... -->

            <div class="countdown mb-3" id="countdown">14:59</div>
            <p class="opacity-90">Mã QR sẽ hết hạn sau 15 phút</p>

            <div class="alert alert-light text-dark mt-4">
                <strong>Lưu ý quan trọng:</strong><br>
                Vui lòng chuyển khoản đúng nội dung:<br>
                <div class="order-code mt-3"><?= $order_code ?></div>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH SẢN PHẨM + TỔNG TIỀN -->
        <div class="col-lg-7 order-section">
            <h3 class="text-primary mb-4 fw-bold">
                Đơn hàng của bạn
            </h3>

            <div style="max-height: 400px; overflow-y: auto;">
                <?php foreach ($items as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                ?>
                    <div class="product-item">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                             onerror="this.src='https://via.placeholder.com/80'">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                            <small class="text-muted">
                                <?= number_format($item['price']) ?>đ × <?= $item['quantity'] ?>
                            </small>
                        </div>
                        <strong class="text-danger"><?= number_format($subtotal) ?>đ</strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">Tổng thanh toán:</h4>
                <div class="total-price"><?= number_format($total) ?>đ</div>
            </div>

            <div class="text-center">
                <a href="order_success.php?code=<?= $order_code ?>" 
                   class="btn btn-pay">
                    Tôi đã chuyển khoản xong
                </a>
                <p class="mt-3">
                    <a href="cart.php" class="text-muted text-decoration-none">
                        Quay lại giỏ hàng
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Đếm ngược 15 phút -->
<script>
let timeLeft = 15 * 60;
const timer = document.getElementById('countdown');
setInterval(() => {
    if (timeLeft <= 0) {
        timer.innerHTML = "Hết hạn!";
        return;
    }
    timeLeft--;
    const minutes = String(Math.floor(timeLeft / 60)).padStart(2, '0');
    const seconds = String(timeLeft % 60).padStart(2, '0');
    timer.innerHTML = minutes + ':' + seconds;
}, 1000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
