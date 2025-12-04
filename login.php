<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Cửa hàng Online</title>

    <!-- Google Fonts + Material Icons -->
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

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 20px;
            padding: 2.8rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .login-header h2 {
            font-weight: 700;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #555;
            text-align: center;
            font-size: 0.95rem;
        }

        .btn-login {
            background: #0d6efd;
            border: none;
            border-radius: 14px;
            padding: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #0b5ed7;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.4);
        }

        .admin-box {
            background: linear-gradient(135deg, #fff8e1, #fff3cd);
            border: 2px solid #ffb300;
            border-radius: 16px;
            padding: 1.3rem;
            margin-top: 2rem;
            text-align: center;
            box-shadow: 0 8px 25px rgba(255, 179, 0, 0.2);
        }

        .btn-admin {
            background: #ff8f00;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            margin-top: 0.8rem;
            transition: all 0.3s;
        }

        .btn-admin:hover {
            background: #f57c00;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(245, 124, 0, 0.4);
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h2>Đăng nhập</h2>
        <p>Chào mừng quay lại cửa hàng online</p>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success text-center"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <form action="validation.php" method="post">
       <div class="mb-4">
    <label class="form-label fw-medium d-flex align-items-center gap-2">
        Tên đăng nhập hoặc Email Gmail
    </label>
    <div class="input-group input-group-lg">
        
        <input 
            type="text" 
            name="login" 
            class="form-control" 
            placeholder="Nhập username hoặc email" 
            value="<?php echo isset($_COOKIE['login']) ? htmlspecialchars($_COOKIE['login']) : ''; ?>" 
            required
            autocomplete="username">
    </div>
    <div class="form-text text-success mt-2">
        Bạn có thể đăng nhập bằng <strong>tên đăng nhập</strong> hoặc <strong>email Gmail</strong>
    </div>
</div>
        <div class="form-floating mb-3">
            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Mật khẩu" required>
            <label for="password">Mật khẩu</label>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="remember" name="remember"
                <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
        </div>

        <button type="submit" class="btn btn-primary btn-login w-100">
            Đăng nhập
        </button>
    </form>

    <div class="text-center mt-4">
        <p>Chưa có tài khoản? <a href="register.php" class="text-primary fw-bold text-decoration-none">Đăng ký ngay</a></p>
    </div>

    <!-- Box tạo Admin đầu tiên -->
    <?php
    require_once 'connection.php';
    $check = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    if ($check->num_rows === 0):
    ?>
    <div class="admin-box">
        <div class="d-flex justify-content-center align-items-center gap-2 text-warning fw-bold">
            <span class="material-icons">warning_amber</span>
            <span>Hệ thống chưa có quản trị viên</span>
        </div>
        <p class="mb-2 mt-2 text-muted small">Bạn cần tạo tài khoản Admin đầu tiên để quản lý hệ thống.</p>
        <a href="register.php?admin=1" class="btn btn-admin">
            Tạo tài khoản Admin đầu tiên
        </a>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>