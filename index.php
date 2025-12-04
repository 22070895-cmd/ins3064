<?php
require_once 'connection.php'; // kết nối CSDL

// Lấy 6 sản phẩm mới nhất làm sản phẩm mẫu
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng Online - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
        }
        .navbar {
            background: rgba(0,0,0,0.3) !important;
            backdrop-filter: blur(10px);
        }
        .hero {
            padding: 100px 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .hero p {
            font-size: 1.4rem;
            max-width: 700px;
            margin: 1.5rem auto;
            opacity: 0.9;
        }
        .product-card {
            background: white;
            color: #333;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .product-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e91e63;
        }
        .footer {
            background: rgba(0,0,0,0.5);
            padding: 3rem 0;
            margin-top: 5rem;
        }
        .btn-custom {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<!-- Navbar với 2 nút Sign In & Sign Up góc phải -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-3 fw-bold" href="index.php">
            Cửa hàng Online
        </a>
        <div class="d-flex gap-3">
            <a href="login.php" class="btn btn-outline-light btn-custom">
                Sign In
            </a>
            <a href="register.php" class="btn btn-light text-primary btn-custom">
                Sign Up
            </a>
        </div>
    </div>
</nav>

<!-- Hero Section - Giới thiệu -->
<section class="hero">
    <div class="container">
        <h1>Chào mừng đến với Cửa hàng Online</h1>
        <p>Mua sắm dễ dàng – Giao hàng nhanh chóng – Giá cả hợp lý!</p>
        <div class="mt-4">
            <a href="#products" class="btn btn-light btn-lg px-5 py-3 btn-custom">Xem sản phẩm</a>
        </div>
    </div>
</section>

<!-- Sản phẩm mẫu -->
<section class="py-5 bg-white text-dark" id="products">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-primary">Sản phẩm nổi bật</h2>
        <div class="row g-4">
            <?php while ($p = $products->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="product-card">
                    <img src="<?= htmlspecialchars($p['image_url']) ?>" 
                         alt="<?= htmlspecialchars($p['name']) ?>"
                         onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                    <div class="p-4">
                        <h5 class="fw-bold"><?= htmlspecialchars($p['name']) ?></h5>
                        <p class="price mt-2"><?= number_format($p['price']) ?>đ</p>
                        <small class="text-muted">Còn lại: <?= $p['quantity'] ?> sản phẩm</small>
                        <div class="mt-3 text-end">
                            <a href="login.php" class="btn btn-primary btn-sm">Mua ngay</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Footer - Thông tin liên hệ -->
<footer class="footer text-center">
    <div class="container">
        <h4>Liên hệ với chúng tôi</h4>
        <p>
            Email: contact@cuahangonline.com<br>
            Hotline: 1900 1234<br>
            Địa chỉ: 123 Đường ABC, Quận 1, TP.HCM
        </p>
        <p class="mt-3">&copy; 2025 Cửa hàng Online. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>