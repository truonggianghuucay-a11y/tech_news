<?php
/**
 * Thanh menu dieu huong & chuyen muc - 100% HTML & CSS thuan
 */
// Lay danh sach tat ca chuyen muc de hien thi len menu
$nav_categories = Database::db_get_list("SELECT * FROM categories ORDER BY id ASC");
$current_cid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$current_kw = isset($_GET['keyword']) ? clean($_GET['keyword']) : '';
?>

<!-- Header chinh: Logo, Tim kiem, Dang nhap -->
<header class="tech-navbar py-3">
    <div class="container">
        <div class="row align-items-center g-3">
            <!-- Logo -->
            <div class="col-md-3 col-6">
                <a href="index.php" class="brand-logo">
                    TECH<span>NEWS</span>
                </a>
            </div>

            <!-- Form tim kiem thuan GET (Khong dung JS) -->
            <div class="col-md-6 col-12 order-md-2 order-3">
                <form action="search.php" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" 
                               placeholder="Tìm kiếm tin tức, thiết bị, AI..." 
                               value="<?= $current_kw ?>" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>

            <!-- Link quan tri / Admin -->
            <div class="col-md-3 col-6 text-end order-md-3 order-2">
                <?php if (Session::get_session('admin_logged')): ?>
                    <a href="admin/index.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-speedometer2"></i> Quản trị
                    </a>
                <?php else: ?>
                    <a href="admin/login.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-shield-lock"></i> Đăng nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Thanh danh muc tin tuc (Cuon ngang tu nhien tren di dong) -->
<nav class="category-bar">
    <div class="container">
        <div class="d-flex align-items-center">
            <a href="index.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php' && $current_cid == 0) ? 'active' : '' ?>">
                <i class="bi bi-house-door"></i> Trang chủ
            </a>
            <?php if (!empty($nav_categories)): ?>
                <?php foreach ($nav_categories as $cat): ?>
                    <a href="category.php?id=<?= $cat['id'] ?>" 
                       class="nav-link <?= ($current_cid == $cat['id']) ? 'active' : '' ?>">
                        <?= clean($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</nav>
