<?php
/**
 * Header giao dien nguoi dung - TechNews
 * Chi su dung Bootstrap 5 CSS va CSS tuy chinh, tuyet doi khong co JavaScript
 */
require_once __DIR__ . '/../config.php';

$page_title = isset($page_title) ? clean($page_title) . ' | TechNews' : 'TechNews - Trang tin tức công nghệ hàng đầu';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <!-- Bootstrap 5 CSS (Chi CSS, khong co JS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS tuy bien cua du an -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
