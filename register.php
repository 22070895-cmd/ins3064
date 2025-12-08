<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.7)),
                        url('apple.jpg') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .card-title {
            font-weight: 700;
            font-size: 1.8rem;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .admin-badge {
            background: linear-gradient(135deg, #ff6b00, #ff8f00);
            color: white;
            padding: 0.7rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 6px 20px rgba(255, 107, 0, 0.4);
            margin-bottom: 1rem;
        }

        .btn-register {
            background: #0d6efd;
            border: none;
            border-radius: 16px;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background: #0b5ed7;
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(13, 110, 253, 0.4);
        }

        .form-text {
            font-size: 0.9rem;
        }

        .gmail-icon {
            color: #ea4335;
            font-size: 1.4rem;
        }
    </style>
</head>
<body>

<div class="register-card">
    <?php
    $isFirstAdmin = false;
    if (isset($_GET['admin']) && $_GET['admin'] == 1) {
        require_once 'connection.php';
        $check = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
        $isFirstAdmin = ($check && $check->num_rows === 0);
    }
    ?>

    <div class="text-center mb-4">
        <h2 class="card-title">
            <?php echo $isFirstAdmin ? 'Tạo tài khoản Admin đầu tiên' : 'Đăng ký tài khoản'; ?>
        </h2>
        <p class="text-muted">
            <?php echo $isFirstAdmin ? 'Bạn đang tạo quản trị viên đầu tiên' : 'Tham gia cùng chúng tôi ngay hôm nay'; ?>
        </p>

        <?php if ($isFirstAdmin): ?>
            <div class="admin-badge">
                QUẢN TRỊ VIÊN
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <?php if ($isFirstAdmin): ?>
        <div class="alert alert-warning text-center mb-4">
            <strong>Chúc mừng!</strong> Bạn đang tạo tài khoản <strong>Admin đầu tiên</strong> của hệ thống.
        </div>
    <?php endif; ?>

    <form action="registration.php" method="post">
        <!-- Tên đăng nhập -->
        <div class="mb-3">
            <label class="form-label fw-medium">Tên đăng nhập</label>
            <div class="input-group">
                
                <input type="text" name="username" class="form-control form-control-lg" 
                       placeholder="Nhập tên đăng nhập" required>
            </div>
        </div>

        <!-- Email Gmail bắt buộc -->
        <div class="mb-3">
            <label class="form-label fw-medium">
                Email 
            </label>
            <div class="input-group">
    
                <input type="email" name="email" class="form-control form-control-lg" 
                       placeholder="abc@gmail.com" required>
            </div>
            <div class="form-text text-success">
                Chỉ chấp nhận email có đuôi @gmail.com
            </div>
        </div>

        <!-- Mật khẩu -->
        <div class="mb-4">
            <label class="form-label fw-medium">Mật khẩu</label>
            <div class="input-group">
               
                <input type="password" name="password" class="form-control form-control-lg" 
                       placeholder="Nhập mật khẩu an toàn" required>
            </div>
        </div>

        <?php if ($isFirstAdmin): ?>
            <input type="hidden" name="make_admin" value="1">
        <?php endif; ?>

        <button type="submit" class="btn btn-primary btn-register w-100">
            <?php echo $isFirstAdmin ? 'Tạo tài khoản Admin' : 'Đăng ký ngay'; ?>
        </button>
    </form>

    <div class="text-center mt-4">
        <p class="text-muted">
            Đã có tài khoản? 
            <a href="login.php" class="text-primary fw-bold text-decoration-none">Đăng nhập ngay</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>