<?php
/**
 * ============================================================
 *  FILE CAU HINH CHUNG - TECHNEWS
 * ============================================================
 *  - Khoi tao session
 *  - Thiet lap mui gio Viet Nam
 *  - Nap thu vien lib.php
 *  - Cac ham tien ich xu ly chuoi, chong XSS, tao slug
 * ============================================================
 */

// 1. Khoi dong session neu chua co
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Thiet lap mui gio Viet Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// 3. Nap thu vien dung chung (Database, Session, paging, Helper)
require_once __DIR__ . '/lib.php';

// 4. Dinh nghia duong dan thu muc uploads tren may chu
define('UPLOAD_PATH', __DIR__ . '/assets/uploads/');

/**
 * Ham lam sach du lieu phong chong tan cong XSS
 * Luon dung ham nay khi hien thi du lieu tu database ra HTML
 */
function clean($data)
{
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Ham cat ngan van ban (chuoi) kem dau ba cham (...)
 */
function str_limit($str, $limit = 120)
{
    $str = strip_tags($str ?? '');
    if (mb_strlen($str) <= $limit) {
        return $str;
    }
    return mb_substr($str, 0, $limit) . '...';
}

/**
 * Ham tao slug tu chuoi tieng Viet (ho tro SEO va URL than thien)
 */
function make_slug($str)
{
    $str = trim(mb_strtolower($str ?? ''));
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
    ];
    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

/**
 * Ham dinh dang ngay gio Viet Nam (d/m/Y H:i)
 */
function format_date($datetime)
{
    if (empty($datetime)) {
        return '';
    }
    return date('d/m/Y H:i', strtotime($datetime));
}
