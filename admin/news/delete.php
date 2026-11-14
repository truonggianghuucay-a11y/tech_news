<?php
/**
 * XAC NHAN VA XOA BAI VIET - TECHNEWS ADMIN
 * 100% Khong su dung JavaScript (confirm())
 * Su dung phuong thuc POST voi buoc xac nhan rieng biet
 */
$admin_page_title = 'Xác nhận xóa bài viết';
require_once __DIR__ . '/../includes/header.php';

$id = (int)Helper::input_value('id');
if ($id <= 0) {
    Helper::redirect('index.php');
}

// Lay thong tin bai viet
$sql = "SELECT n.*, c.name as category_name 
        FROM news n 
        JOIN categories c ON n.category_id = c.id 
        WHERE n.id = :id";
$news = Database::db_get_row($sql, ['id' => $id]);

if (!$news) {
    Helper::redirect('index.php');
}

// Xu ly XOA khi nguoi dung bam nut POST "Xac nhan xoa"
if (Helper::is_submit('confirm_delete')) {
    // 1. Xoa file anh dai dien neu ton tai trong thu muc uploads
    if (!empty($news['thumbnail']) && file_exists(UPLOAD_PATH . $news['thumbnail'])) {
        @unlink(UPLOAD_PATH . $news['thumbnail']);
    }

    // 2. Xoa ban ghi khoi CSDL
    Database::db_execute("DELETE FROM news WHERE id = :id", ['id' => $id]);

    // 3. Chuyen huong ve danh sach
    Helper::redirect('index.php');
}
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../index.php">Tổng quan</a></li>
            <li class="breadcrumb-item"><a href="index.php">Bài viết</a></li>
            <li class="breadcrumb-item active" aria-current="page">Xác nhận xóa</li>
        </ol>
    </nav>
</div>

<!-- Khung xac nhan xoa thuan HTML & CSS -->
<div class="delete-confirm-box text-center">
    <div class="mb-3 text-danger fs-1">
        <i class="bi bi-trash3-fill"></i>
    </div>

    <h4 class="fw-bold text-danger mb-2">Xác nhận xóa bài viết</h4>
    <p class="text-secondary mb-3">
        Bạn có chắc chắn muốn xóa bài viết này không? Hành động này sẽ không thể khôi phục!
    </p>

    <div class="p-3 bg-light rounded-3 text-start small mb-4">
        <div class="mb-2"><strong>Mã bài viết (ID):</strong> <?= $news['id'] ?></div>
        <div class="mb-2"><strong>Tiêu đề:</strong> <span class="fw-bold text-dark"><?= clean($news['title']) ?></span></div>
        <div class="mb-2"><strong>Chuyên mục:</strong> <?= clean($news['category_name']) ?></div>
        <div class="mb-2"><strong>Số lượt đọc:</strong> <?= number_format($news['views']) ?></div>
        <div><strong>Ngày đăng:</strong> <?= format_date($news['created_at']) ?></div>

        <?php if (!empty($news['thumbnail'])): ?>
            <div class="mt-3 pt-2 border-top">
                <span class="text-secondary d-block mb-1">Ảnh đại diện đi kèm:</span>
                <img src="<?= $site_root ?>assets/uploads/<?= clean($news['thumbnail']) ?>" alt="thumb" style="max-height: 80px; border-radius: 4px;" class="border">
            </div>
        <?php endif; ?>
    </div>

    <!-- Form POST xac nhan xoa (Tuyet doi khong dung JS) -->
    <form action="delete.php?id=<?= $id ?>" method="POST" class="d-inline-flex gap-3 justify-content-center">
        <input type="hidden" name="action" value="confirm_delete">
        <input type="hidden" name="id" value="<?= $id ?>">

        <button type="submit" class="btn btn-danger px-4">
            <i class="bi bi-trash-fill"></i> Có, xóa bài viết này
        </button>
        <a href="index.php" class="btn btn-secondary px-4">
            <i class="bi bi-arrow-left"></i> Không, quay lại
        </a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
