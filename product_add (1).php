<?php
require_once 'auth.php';
require_role('admin');
require_once 'connection.php';

$error = $success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
        $image_url = 'https://via.placeholder.com/400x400.png?text=No+Image';
    }

    if ($name && $price >= 0 && $quantity >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, image_url, price, quantity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $name, $image_url, $price, $quantity);
        if ($stmt->execute()) {
            $success = "Thêm sản phẩm thành công!";
        } else {
            $error = "Lỗi khi thêm sản phẩm.";
        }
        $stmt->close();
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin hợp lệ.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('image.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .form-card {
            background: rgba(255,255,255,0.97);
            border-radius: 20px;
            padding: 2.8rem;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }
        .btn-save { background: #0d6efd; border-radius: 14px; padding: 0.9rem; font-weight: 600; }
        .btn-save:hover { background: #0b5ed7; transform: translateY(-3px); }
    </style>
</head>
<body>
<div class="form-card">
    <div class="text-center mb-4">
        <span class="material-icons" style="font-size: 70px; color: #0d6efd;">add_box</span>
        <h2 class="fw-bold mt-3">Thêm sản phẩm mới</h2>
    </div>

    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label class="form-label fw-medium">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control form-control-lg" placeholder="iPhone 15 Pro" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Link ảnh sản phẩm (URL)</label>
            <input type="url" name="image_url" class="form-control form-control-lg" 
                   placeholder="https://example.com/image.jpg" 
                   value="https://via.placeholder.com/400x400.png?text=New+Product">
            <div class="form-text">Dán link ảnh từ Imgur, Postimages, Unsplash...</div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Giá bán (VNĐ)</label>
            <input type="number" name="price" class="form-control form-control-lg" min="0" step="1000" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium">Số lượng tồn kho</label>
            <input type="number" name="quantity" class="form-control form-control-lg" min="0" required>
        </div>

        <div class="d-flex gap-3 justify-content-center">
            <a href="home.php" class="btn btn-secondary btn-lg">Quay lại</a>
            <button type="submit" class="btn btn-primary btn-save text-white">
                <span class="material-icons align-middle me-2">save</span> Lưu sản phẩm
            </button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>