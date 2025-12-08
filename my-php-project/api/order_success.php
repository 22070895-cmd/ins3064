<?php
// BẮT BUỘC để tránh lỗi session trên Vercel
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/connection.php';

require_login();

$user_id = $_SESSION['user_id'];
$order_code = $_GET['code'] ?? '';

// Nếu không có mã đơn → quay lại giỏ
if (!$order_code) {
    header('Location: cart.php');
    exit();
}

// KIỂM TRA ĐƠN ĐÃ ĐƯỢC TẠO CHƯA (tránh tạo trùng)
$check = $conn->query("SELECT id, total_amount FROM orders WHERE order_code = '$order_code' AND user_id = $user_id");
if ($check->num_rows > 0) {
    // Đã tạo rồi → chỉ hiển thị thông báo
    $order = $check->fetch_assoc();
    $total = $order['total_amount'];
} else {
    // TẠO ĐƠN HÀNG MỚI
    $cart = $conn->query("
        SELECT c.*, p.price, p.quantity as stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id
    ");

    if ($cart->num_rows == 0) {
        header('Location: cart.php');
        exit();
    }

    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $conn->autocommit(false);
    try {
        // 1. Tạo đơn hàng
        $stmt = $conn->prepare("INSERT INTO orders (order_code, user_id, total_amount, status) VALUES (?, ?, ?, 'paid')");
        $stmt->bind_param("sid", $order_code, $user_id, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;
        $stmt->close();

        // 2. Thêm chi tiết đơn hàng + giảm tồn kho
        foreach ($cart as $item) {
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt2->execute();
            $stmt2->close();

            $conn->query("UPDATE products SET quantity = quantity - {$item['quantity']} WHERE id = {$item['product_id']}");
        }

        // 3. XÓA GIỎ HÀNG
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Lỗi hệ thống: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công!</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.7)),
                        url('white.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 30px;
            padding: 2.0rem 1.5rem;
            text-align: center;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
        }
        .icon-success {
            font-size: 100px;
            color: #28a745;
            
    
        }
        .title {
            font-size: 2.0rem;
            font-weight: 800;
            color: #28a745;
            margin: 1.5rem 0;
        }
        .order-code-box {
            background: #fff0f0;
            color: #e91e63;
            padding: 1.5rem 2.5rem;
            border-radius: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            display: inline-block;
            margin: 1.5rem 0;
            box-shadow: 0 8px 25px rgba(233, 30, 99, 0.15);
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fa0101ff;
        }
        .btn-custom {
            padding: 1rem 3rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0.8rem;
        }
    </style>
</head>
<body>
<div class="success-card">
    <span class="material-icons icon-success">check_circle</span>
    <h1 class="title">Thanh toán thành công!</h1>
    <p class="fs-4 text-muted mb-4">Cảm ơn bạn đã tin tưởng và mua sắm tại cửa hàng!</p>

    <div class="order-code-box">
        Mã đơn hàng của bạn:<br>
        <strong class="fs-3"><?= htmlspecialchars($order_code) ?></strong>
    </div>

    <p class="total-price">
        Tổng tiền thanh toán: <strong><?= number_format($total) ?>đ</strong>
    </p>

    <p class="text-muted fs-5">
        Đơn hàng đã được ghi nhận và sẽ được xử lý sớm nhất có thể!
    </p>

    <div class="mt-5">
        <a href="products.php" class="btn btn-primary btn-custom btn-lg">
            Tiếp tục mua sắm
        </a>
        <?php if (is_admin()): ?>
            <a href="home.php" class="btn btn-outline-success btn-custom btn-lg">
                Vào trang Admin
            </a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>