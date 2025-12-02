<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'], $_POST['quantity'])) {
    header('Location: products.php?error=Dữ liệu không hợp lệ');
    exit();
}

$productId = (int) $_POST['product_id'];
$quantity = max(1, (int) $_POST['quantity']);
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: products.php?error=Không tìm thấy sản phẩm');
    exit();
}

if ($product['quantity'] < $quantity) {
    header('Location: products.php?error=Số lượng vượt quá tồn kho');
    exit();
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iii", $userId, $productId, $quantity);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $productId);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    header('Location: products.php?message=Đặt hàng thành công');
    exit();
} catch (Throwable $e) {
    $conn->rollback();
    header('Location: products.php?error=Không thể đặt hàng, vui lòng thử lại');
    exit();
}

