<?php
/**
 * Sidebar Menu Quan Tri - 100% HTML & CSS thuan
 */
$page_uri = $_SERVER['PHP_SELF'];
?>
<aside class="admin-sidebar">
    <a href="<?= $admin_root ?>index.php" class="admin-brand">
        TECH<span>ADMIN</span>
    </a>

    <ul class="admin-menu">
        <li class="admin-menu-item">
            <a href="<?= $admin_root ?>index.php" class="admin-menu-link <?= (basename($page_uri) == 'index.php' && !$is_sub) ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Tổng quan
            </a>
        </li>

        <li class="px-3 pt-3 pb-1 text-uppercase text-secondary" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">
            Quản lý nội dung
        </li>

        <li class="admin-menu-item">
            <a href="<?= $admin_root ?>categories/index.php" class="admin-menu-link <?= (strpos($page_uri, '/categories/') !== false) ? 'active' : '' ?>">
                <i class="bi bi-folder2-open"></i> Chuyên mục
            </a>
        </li>

        <li class="admin-menu-item">
            <a href="<?= $admin_root ?>news/index.php" class="admin-menu-link <?= (strpos($page_uri, '/news/') !== false) ? 'active' : '' ?>">
                <i class="bi bi-newspaper"></i> Bài viết tin tức
            </a>
        </li>

        <li class="px-3 pt-3 pb-1 text-uppercase text-secondary" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px;">
            Lối tắt nhanh
        </li>

        <li class="admin-menu-item">
            <a href="<?= $admin_root ?>news/add.php" class="admin-menu-link">
                <i class="bi bi-plus-circle"></i> Đăng bài mới
            </a>
        </li>

        <li class="admin-menu-item">
            <a href="<?= $admin_root ?>categories/add.php" class="admin-menu-link">
                <i class="bi bi-folder-plus"></i> Thêm chuyên mục
            </a>
        </li>
    </ul>

    <div class="admin-user-box">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-circle fs-4 text-primary"></i>
            <div class="text-truncate">
                <div class="fw-bold text-white"><?= clean($admin_fullname) ?></div>
                <div class="small text-muted">Administrator</div>
            </div>
        </div>
    </div>
</aside>
