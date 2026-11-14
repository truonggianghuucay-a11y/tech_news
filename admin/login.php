<?php
/**
 * TRANG DANG NHAP QUANTRI - TECHNEWS
 * Xu ly dang nhap bang Session + password_verify() + Prepared Statements
 * Khong su dung JavaScript
 */
require_once __DIR__ . '/../config.php';

// Neu da dang nhap roi thi chuyen thang vao Dashboard
if (Session::get_session('admin_logged')) {
    Helper::redirect('index.php');
}

$error = '';
$username = '';

// Xu ly khi nguoi dung bam nut Dang nhap (POST)
if (Helper::is_submit('login')) {
    $username = trim(Helper::input_value('username'));
    $password = trim(Helper::input_value('password'));

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
    } else {
        // Truy van thong tin quan tri vien bang Prepared Statement
        $admin = Database::db_get_row("SELECT * FROM admins WHERE username = :u", ['u' => $username]);

        // Kiem tra mat khau da ma hoa bang password_verify()
        if ($admin && password_verify($password, $admin['password'])) {
            // Dang nhap thanh cong -> Luu session
            Session::set_session('admin_logged', true);
            Session::set_session('admin_id', $admin['id']);
            Session::set_session('admin_username', $admin['username']);
            Session::set_session('admin_fullname', $admin['fullname']);

            // Chuyen huong vao Dashboard
            Helper::redirect('index.php');
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị - TechNews</title>
    <!-- Bootstrap 5 CSS (Chi CSS, Khong co JS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .login-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: #0f172a;
            text-align: center;
            display: block;
            margin-bottom: 0.5rem;
            text-decoration: none;
        }
        .login-brand span {
            color: #2563eb;
        }
    </style>
</head>
<body>

<div class="login-card">
    <a href="../index.php" class="login-brand">
        TECH<span>NEWS</span>
    </a>
    <p class="text-center text-muted small mb-4">Hệ thống Đăng nhập Quản trị viên</p>

    <!-- Thong bao loi (neu co) -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= clean($error) ?>
        </div>
    <?php endif; ?>

    <!-- Form dang nhap POST thuan -->
    <form action="login.php" method="POST">
        <!-- Truong action xac thuc submit bang Helper::is_submit('login') -->
        <input type="hidden" name="action" value="login">

        <div class="mb-3">
            <label for="username" class="form-label fw-semibold small">Tên đăng nhập</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                <input type="text" name="username" id="username" class="form-control" 
                       value="<?= clean($username) ?>" placeholder="Nhập username" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold small">Mật khẩu</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                <input type="password" name="password" id="password" class="form-control" 
                       placeholder="Nhập mật khẩu" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập hệ thống
        </button>
    </form>

    <div class="mt-4 pt-3 border-top text-center">
        <a href="../index.php" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left"></i> Quay lại trang chủ TechNews
        </a>
        <div class="mt-2 text-muted" style="font-size: 0.75rem;">
            Tài khoản mặc định: <strong>admin</strong> / Mật khẩu: <strong>admin123</strong>
        </div>
    </div>
</div>

</body>
</html>
