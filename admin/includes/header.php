<?php
/**
 * Header Admin - TechNews
 * Kiem tra Session dang nhap, chi nhung CSS, tuyet doi khong co JavaScript
 */
require_once __DIR__ . '/../../config.php';

// Xac dinh duong dan tuong doi toi thu muc admin/ va root
$current_path = str_replace('\\', '/', $_SERVER['PHP_SELF']);
$is_sub = (strpos($current_path, '/categories/') !== false || strpos($current_path, '/news/') !== false);
$admin_root = $is_sub ? '../' : './';
$site_root = $is_sub ? '../../' : '../';

// KIEM TRA SESSION DANG NHAP
if (!Session::get_session('admin_logged')) {
    Helper::redirect($admin_root . 'login.php');
}

$admin_fullname = Session::get_session('admin_fullname') ?: 'Quản trị viên';
$admin_username = Session::get_session('admin_username') ?: 'admin';
$admin_page_title = isset($admin_page_title) ? clean($admin_page_title) . ' - TechNews Admin' : 'Quản trị TechNews';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $admin_page_title ?></title>
    <!-- Bootstrap 5 CSS (Chi CSS, KHONG DUNG JS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS Quan tri -->
    <link rel="stylesheet" href="<?= $site_root ?>assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar ben trai -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Khung noi dung chinh -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div>
                <a href="<?= $site_root ?>index.php" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right"></i> Xem trang chủ
                </a>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-secondary small">
                    Xin chào, <strong class="text-dark"><?= clean($admin_fullname) ?></strong> (<?= clean($admin_username) ?>)
                </span>
                <a href="<?= $admin_root ?>logout.php" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>
            </div>
        </header>

        <!-- Noi dung cu the tung trang -->
        <main class="admin-content">
