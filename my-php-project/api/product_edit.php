<?php
require_once 'auth.php';
require_role('admin');
require_once 'connection.php';

$id = $_GET['id'] ?? 0;
$error = $success = null;

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) { header("Location: home.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);

    if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
        $image_url = $product['image_url'];
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, image_url=?, price=?, quantity=? WHERE id=?");
    $stmt->bind_param("ssdii", $name, $image_url, $price, $quantity, $id);
    if ($stmt->execute()) {
        $success = "Cập nhật thành công!";
        $product = array_merge($product, ['name'=>$name, 'image_url'=>$image_url, 'price'=>$price, 'quantity'=>$quantity]);
    } else {
        $error = "Lỗi khi cập nhật.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('assets/image.jpg') center/cover fixed; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .form-card { background: rgba(255,255,255,0.97); border-radius: 20px; padding: 2.8rem; max-width: 540px; box-shadow: 0 20px 50px rgba(0,0,0,0.4); }
        .product-img { width: 100%; max-height: 250px; object-fit: contain; border-radius: 16px; }
        .btn-update { background: #ff6b00; border-radius: 14px; padding: 0.9rem; font-weight: 600; }
        .btn-update:hover { background: #e65100; }
    </style>
</head>
<body>
<div class="form-card">
    <div class="text-center mb-4">
        <span class="material-icons" style="font-size: 70px; color: #ff6b00;">edit_note</span>
        <h2 class="fw-bold mt-3">Chỉnh sửa sản phẩm</h2>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

    <div class="text-center mb-4">
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
             class="product-img img-fluid" 
             alt="Ảnh sản phẩm"
             onerror="this.src='https://via.placeholder.com/400?text=No+Image'">
    </div>

    <form method="POST">
        <div class="mb-4">
            <label class="form-label fw-medium">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control form-control-lg" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Link ảnh sản phẩm</label>
            <input type="url" name="image_url" class="form-control form-control-lg" value="<?php echo htmlspecialchars($product['image_url']); ?>">
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Giá bán</label>
            <input type="number" name="price" class="form-control form-control-lg" value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Số lượng</label>
            <input type="number" name="quantity" class="form-control form-control-lg" value="<?php echo $product['quantity']; ?>" required>
        </div>

        <div class="d-flex gap-3 justify-content-center">
            <a href="/api/home.php" class="btn btn-secondary btn-lg">Quay lại</a>
            <button type="submit" class="btn btn-update text-white">
                <span class="material-icons align-middle me-2">update</span> Cập nhật
            </button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>