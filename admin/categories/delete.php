<?php
/**
 * XAC NHAN VA XOA CHUYEN MUC - TECHNEWS ADMIN
 * 100% Khong su dung JavaScript (confirm())
 * Su dung phuong thuc POST voi buoc xac nhan rieng biet
 */
$admin_page_title = 'Xác nhận xóa chuyên mục';
require_once __DIR__ . '/../includes/header.php';

$id = (int)Helper::input_value('id');
if ($id <= 0) {
    Helper::redirect('index.php');
}

// Lay thong tin chuyen muc
$category = Database::db_get_row("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
if (!$category) {
    Helper::redirect('index.php');
}

// Kiem tra so luong bai viet dang thuoc chuyen muc nay
$count_news = (int)Database::db_get_value("SELECT COUNT(*) FROM news WHERE category_id = :cid", ['cid' => $id]);

// Xu ly XOA khi nguoi dung bam nut POST "Xac nhan xoa"
if (Helper::is_submit('confirm_delete')) {
    // Thuc hien xoa chuyen muc trong CSDL
    Database::db_execute("DELETE FROM categories WHERE id = :id", ['id' => $id]);
    Helper::redirect('index.php');
}
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../index.php">Tổng quan</a></li>
            <li class="breadcrumb-item"><a href="index.php">Chuyên mục</a></li>
            <li class="breadcrumb-item active" aria-current="page">Xác nhận xóa</li>
        </ol>
    </nav>
</div>

<!-- Khung canh bao va xac nhan xoa thuan HTML & CSS -->
<div class="delete-confirm-box text-center">
    <div class="mb-3 text-danger fs-1">
        <i class="bi bi-exclamation-octagon-fill"></i>
    </div>
    
    <h4 class="fw-bold text-danger mb-2">Xác nhận xóa Chuyên mục</h4>
    <p class="text-secondary mb-3">
        Bạn có chắc chắn muốn xóa chuyên mục: <strong class="text-dark">"<?= clean($category['name']) ?>"</strong>?
    </p>

    <?php if ($count_news > 0): ?>
        <div class="alert alert-danger py-2 small mb-4 text-start">
            <i class="bi bi-exclamation-triangle-fill"></i> 
            <strong>Cảnh báo quan trọng:</strong> Chuyên mục này hiện đang có <strong><?= $count_news ?></strong> bài viết liên kết. Nếu bạn xóa chuyên mục này, các bài viết thuộc chuyên mục cũng sẽ bị xóa vĩnh viễn theo ràng buộc CSDL!
        </div>
    <?php endif; ?>

    <div class="p-3 bg-light rounded-3 text-start small mb-4">
        <div><strong>Mã chuyên mục (ID):</strong> <?= $category['id'] ?></div>
        <div><strong>Tên chuyên mục:</strong> <?= clean($category['name']) ?></div>
        <div><strong>Đường dẫn (Slug):</strong> <?= clean($category['slug']) ?></div>
        <div><strong>Số bài viết liên kết:</strong> <?= $count_news ?> bài</div>
    </div>

    <!-- Form POST xac nhan xoa (Tuyet doi khong dung JS) -->
    <form action="delete.php?id=<?= $id ?>" method="POST" class="d-inline-flex gap-3 justify-content-center">
        <input type="hidden" name="action" value="confirm_delete">
        <input type="hidden" name="id" value="<?= $id ?>">

        <button type="submit" class="btn btn-danger px-4">
            <i class="bi bi-trash-fill"></i> Có, xóa vĩnh viễn
        </button>
        <a href="index.php" class="btn btn-secondary px-4">
            <i class="bi bi-arrow-left"></i> Không, quay lại
        </a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
