<?php
require_once 'connection.php';

// Lấy 6 sản phẩm mới nhất
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 12");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apple Online - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body, html { height: 100%; overflow-x: hidden; font-family: 'Roboto', sans-serif; }

        /* VIDEO NỀN ĐỘNG */
        #bg-video {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }

        /* LỚP PHỦ TÍM ĐẸP NHƯ ẢNH BẠN GỬI */
        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%, rgba(118, 75, 162, 0.95) 100%);
            z-index: -1;
        }

        /* Navbar trong suốt */
        .navbar {
            background: rgba(0,0,0,0.25) !important;
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        /* Hero Section */
        .hero {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 5rem;
            font-weight: 700;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
            animation: fadeInDown 1.5s ease-out;
        }

        .hero p {
            font-size: 1.6rem;
            max-width: 800px;
            margin: 2rem auto;
            opacity: 0.95;
            animation: fadeInUp 1.8s ease-out;
        }

        .btn-explore {
            background: white;
            color: #333;
            font-weight: 600;
            padding: 1.2rem 3.5rem;
            border-radius: 50px;
            font-size: 1.4rem;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            transition: all 0.4s;
            animation: fadeInUp 2s ease-out;
        }

        .btn-explore:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            background: #f0f0f0;
        }

        /* Sản phẩm nổi bật */
        .products-section {
            background: white;
            color: #333;
            padding: 5rem 0;
        }

        .product-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.4s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .product-card:hover {
            transform: translateY(-20px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
        }

        .product-card img {
            height: 220px;
            object-fit: cover;
        }

        .price {
            font-size: 1.6rem;
            font-weight: 700;
            color: #e91e63;
        }

        /* Footer */
        .footer {
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }

        /* Hiệu ứng */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-60px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(60px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 3.2rem; }
            .hero p { font-size: 1.2rem; }
            .btn-explore { padding: 1rem 2.5rem; font-size: 1.2rem; }
        }
    </style>
</head>
<body>

    <!-- VIDEO NỀN ĐỘNG SIÊU ĐẸP (iPhone 16 Pro chính hãng Apple) -->
    <video autoplay muted loop playsinline id="bg-video" preload="auto">
        <source src="https://www.apple.com/105/media/us/iphone-16-pro/2024/63d8d8af-642b-4652-9809-68911a00767b/anim/sequence/large/01-hero-iphone-16-pro/iphone-16-pro-hero.mp4" type="video/mp4">
        <!-- Video dự phòng nếu không load được -->
        <source src="https://cdn.shopify.com/videos/c/o/v/4e9d3c6d8a9f4f7b8e8d6b5f8d9a6c8e.mp4" type="video/mp4">
    </video>

    <div class="overlay"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fs-3 fw-bold" href="index.php">Apple Online</a>
            <div class="d-flex gap-3">
                <a href="login.php" class="btn btn-outline-light">Sign In</a>
                <a href="register.php" class="btn btn-light text-primary">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h1>Chào mừng đến với Apple Online</h1>
            <p>Mua sắm dễ dàng – Giao hàng nhanh chóng – Giá cả hợp lý!</p>
            <a href="#products" class="btn btn-explore">Xem sản phẩm</a>
        </div>
    </section>

    <!-- Sản phẩm nổi bật -->
    <section class="products-section" id="products">
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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <h4>Liên hệ với chúng tôi</h4>
            <p>
                Email: nguyenthaivietanh241204@gmail.com<br>
                Hotline: 0862 278 883<br>
                Địa chỉ: 165 Cầu Giấy, Hà Nội
            </p>
            <p class="mt-3">&copy; 2025 Apple Online. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>