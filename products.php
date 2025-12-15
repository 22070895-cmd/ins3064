<?php
require_once 'auth.php';
require_login();
require_once 'connection.php';

$user = current_user();

// XỬ LÝ TÌM KIẾM
$search = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM products";
$params = [];
$types = '';

if ($search !== '') {
    $sql .= " WHERE name LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

$sql .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Đếm giỏ hàng
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $res = $conn->query("SELECT SUM(quantity) FROM cart WHERE user_id = " . (int)$_SESSION['user_id']);
    $cart_count = $res->fetch_row()[0] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apple Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Roboto', sans-serif; 
            background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url('white.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            margin: 0;
        }
        .navbar { 
            background: #2a2c2dff !important; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            padding: 1rem 0;
        }
        .search-container { max-width: 600px; margin: 0 auto; }
        .search-box { border-radius: 50px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.2); }
        .search-box input { border: none; padding: 1rem 1.5rem; font-size: 1.1rem; background: white; }
        .search-box button { border-radius: 0 50px 50px 0 !important; padding: 0 2.5rem; background: #0d6efd; border: none; }
        .search-box button:hover { background: #0b5ed7; }
        .card-product { border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.12); transition: all 0.4s; }
        .card-product:hover { transform: translateY(-12px); box-shadow: 0 20px 50px rgba(0,0,0,0.2); }
        .card-product img { height: 240px; object-fit: cover; }
        .price { font-size: 1.6rem; font-weight: 700; color: #e91e63; }
        .btn-add-cart { background: #ff9800; color: white; border-radius: 14px; font-weight: 600; padding: 0.8rem 2rem; }
        .btn-add-cart:hover { background: #e68900; }

        /* PHẦN TIN TỨC SẢN PHẨM */
        .news-section {
            background: white;
            padding: 4rem 0;
            margin-top: 4rem;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .news-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            transition: all 0.4s;
        }
        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }
        .news-card img {
            height: 200px;
            object-fit: cover;
        }
        .news-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a1a;
        }
        .hover-shadow {
        transition: all 0.4s ease;
        cursor: pointer;
    }
    .hover-shadow:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15) !important;
        background: #f8f9fa !important;
    }
    .hover-primary:hover {
        color: #0d6efd !important;
    }

        /* FOOTER LIÊN HỆ */
        .footer-contact {
            background: #2a2828ff;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        .footer-contact a {
            color: #4d72a9ff;
            text-decoration: none;
        }
        .footer-contact a:hover {
            text-decoration: underline;
        }
        .social-icon {
            font-size: 1.5rem;
            margin: 0 1rem;
            color: white;
            
        }
        .social-icon:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4 fw-bold" href="products.php">Apple Online</a>

        <div class="search-container">
            <form action="products.php" method="GET" class="d-flex">
                <div class="input-group search-box">
                    <input type="text" name="q" class="form-control" 
                           placeholder="Tìm kiếm sản phẩm..." 
                           value="<?= htmlspecialchars($search) ?>" autofocus>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>

        <div class="d-flex align-items-center gap-3">
            <?php if (is_admin()): ?><a href="home.php" class="btn btn-light btn-sm">Admin</a><?php endif; ?>
            <a href="cart.php" class="btn btn-outline-light position-relative">
                Giỏ hàng
                <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $cart_count ?>
                    </span>
                <?php endif; ?>
            </a>
            <span class="text-white"><?= htmlspecialchars($user['username']) ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Đăng Xuất</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <?php if ($search !== ''): ?>
        <div class="text-center mb-4">
            <p class="text-muted">
                Kết quả tìm kiếm cho: <strong>"<?= htmlspecialchars($search) ?>"</strong> 
                → Tìm thấy <strong><?= $products->num_rows ?> sản phẩm</strong>
            </p>
        </div>
    <?php endif; ?>

    <?php if ($products->num_rows == 0): ?>
        <div class="text-center py-5">
            <span class="material-icons" style="font-size: 120px; color: #ccc;">search_off</span>
            <h3 class="mt-4 text-muted">Không tìm thấy sản phẩm nào</h3>
            <a href="products.php" class="btn btn-primary btn-lg mt-3">Xem tất cả sản phẩm</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            <?php while ($p = $products->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 card-product">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['name']) ?>" onerror="this.src='https://via.placeholder.com/400x300.png?text=No+Image'">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
                            <p class="price mt-2"><?= number_format($p['price'], 0, ',', '.') ?> đ</p>
                            <p class="text-muted mb-3">Còn lại: <strong><?= $p['quantity'] ?></strong></p>
                            <?php if ($p['quantity'] > 0): ?>
                                <form action="cart_add.php" method="POST" class="mt-auto">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <div class="input-group">
                                        <input type="number" name="quantity" min="1" max="<?= $p['quantity'] ?>" value="1" class="form-control" style="max-width:90px;">
                                        <button type="submit" class="btn btn-add-cart">Thêm vào giỏ</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="text-danger text-center fw-bold">Hết hàng</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<!-- PHẦN TIN TỨC SẢN PHẨM -->
<section class="news-section">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-primary">Tin tức & Sự kiện nổi bật</h2>
        <div class="row g-4 text-center">
            <!-- Tin 1 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://www.apple.com/vn/iphone-16-pro/" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           iPhone 16 Pro & iPhone 16 Pro Max ra mắt
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 2 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://www.apple.com/vn/macbook-pro/" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           MacBook Pro M4 – Hiệu năng vượt trội
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 3 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://www.apple.com/vn/airpods-pro/" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           AirPods Pro thế hệ mới – Chống ồn đỉnh cao
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 4 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="cellhttps://cellphones.com.vn/sforum/apple-watch-khong-goi-duoc" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           Nguyên nhân, cách khắc phục lỗi Apple Watch không gọi được
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 5 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/sleep-score-la-gi" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           Sleep Score (Điểm số giấc ngủ) là gì? Tính năng mới trên Apple Watch
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 6 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/dung-luong-toi-da-pin-iphone-tut-nhanh" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           Tại sao dung lượng tối đa pin iphone tụt nhanh? Cách hạn chế
                        </a>
                    </h5>
                </div>
            </div>


            <!-- Tin 7 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/apple-khuyen-nghi-nguoi-dung-nang-cap-ios-26" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                          Apple khuyến nghị người dùng iPhone nâng cấp từ iOS 18 lên iOS 26
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 8 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/camera-iphone-15-pro-max-bi-mo" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                          Khắc phục camera iPhone 15 Pro Max bị mờ dễ dàng, hiệu quả
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 9 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/caviar-ra-mat-iphone-17-pro-secret-love" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           Caviar ra mắt bộ sưu tập iPhone 17 Pro Secret Love: Thiết kế đậm chất lễ hội, giá đắt ngang kim hoàn
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 10 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/5-ly-do-len-doi-macbook-pro-m5" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           MacBook Pro M5 14 inch có đáng mua? Đây là 5 điểm nâng cấp khiến bạn phải nâng đời!
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 11 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/macbook-khong-ket-noi-duoc-bluetooth" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           Cách sửa nhanh MacBook không kết nối được Bluetooth
                        </a>
                    </h5>
                </div>
            </div>

            <!-- Tin 12 -->
            <div class="col-md-4">
                <div class="p-4 bg-light rounded-3 shadow-sm hover-shadow">
                    <h5 class="fw-bold mb-3">
                        <a href="https://cellphones.com.vn/sforum/macbook-bao-nhieu-inch" 
                           target="_blank" 
                           class="text-decoration-none text-dark hover-primary">
                           MacBook bao nhiêu inch? Kích thước các dòng MacBook chi tiếtMacBook bao nhiêu inch? Kích thước các dòng MacBook chi tiết
                        </a>
                    </h5>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- FOOTER - THÔNG TIN LIÊN HỆ -->
<footer class="footer-contact text-center">
    <div class="container">
        <h3 class="mb-4">Liên hệ với chúng tôi</h3>
        <p class="fs-5">
            Email: nguyenthaivietanh241204@gmail.com <br>
            Hotline: <strong>0862 278 883</strong> (8h-22h)<br>
            Địa chỉ: 165 Cầu Giấy, Hà Nội
        </p>
        <div class="mt-4">
            <a href="https://www.facebook.com/anhss.nguyen.165">Facebook</a>
            <a href="https://www.youtube.com/@anhnguyenthaiviet6763">Youtube</a>
            <a href="https://www.instagram.com/vietanh04.__/">Instagram</a>
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>