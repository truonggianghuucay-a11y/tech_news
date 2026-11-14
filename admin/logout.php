<?php
/**
 * DANG XUAT QUAN TRI VIEN - TECHNEWS
 * Xoa du lieu Session va chuyen huong ve trang login
 */
require_once __DIR__ . '/../config.php';

Session::delete_session('admin_logged');
Session::delete_session('admin_id');
Session::delete_session('admin_username');
Session::delete_session('admin_fullname');

// Chuyen huong ve trang dang nhap
Helper::redirect('login.php');
