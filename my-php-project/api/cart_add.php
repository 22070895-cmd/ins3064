<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

$product_id = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

if ($product_id <= 0) {
    header('Location: products.php?error=Sản phẩm không hợp lệ');
    exit();
}

// Kiểm tra tồn kho
$stmt = $conn->prepare("SELECT quantity, name FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product || $product['quantity'] < $quantity) {
    header('Location: products.php?error=Sản phẩm không đủ hàng');
    exit();
}

$user_id = $_SESSION['user_id'];

// Kiểm tra đã có trong giỏ chưa
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Cập nhật số lượng
    $row = $result->fetch_assoc();
    $new_qty = $row['quantity'] + $quantity;
    if ($new_qty > $product['quantity']) $new_qty = $product['quantity'];
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $new_qty, $user_id, $product_id);
} else {
    // Thêm mới
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
}
$stmt->execute();
$stmt->close();

header('Location: cart.php?success=Đã thêm vào giỏ hàng');
exit();
?>