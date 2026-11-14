<?php
/**
 * DANG BAI VIET MOI - TECHNEWS ADMIN
 * Xu ly upload file bang PHP ($_FILES), kiem tra dinh dang va kich thuoc an toan
 */
$admin_page_title = 'Đăng bài viết mới';
require_once __DIR__ . '/../includes/header.php';

// Lay danh sach tat ca chuyen muc de dua vao dropdown
$categories = Database::db_get_list("SELECT id, name FROM categories ORDER BY name ASC");

$error = '';
$title = '';
$category_id = 0;
$summary = '';
$content = '';
$status = 1;

// Xu ly khi submit form POST
if (Helper::is_submit('add_news')) {
    $title = trim(Helper::input_value('title'));
    $category_id = (int)Helper::input_value('category_id');
    $summary = trim(Helper::input_value('summary'));
    $content = trim(Helper::input_value('content'));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    // Kiem tra hop le
    if (empty($title)) {
        $error = 'Vui lòng nhập tiêu đề bài viết!';
    } elseif ($category_id <= 0) {
        $error = 'Vui lòng chọn chuyên mục cho bài viết!';
    } elseif (empty($summary)) {
        $error = 'Vui lòng nhập tóm tắt (mô tả ngắn)!';
    } elseif (empty($content)) {
        $error = 'Vui lòng nhập nội dung chi tiết bài viết!';
    } else {
        $thumbnail_name = null;

        // Xu ly Upload anh dai dien bang PHP thuan
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['thumbnail']['tmp_name'];
            $file_name = $_FILES['thumbnail']['name'];
            $file_size = $_FILES['thumbnail']['size'];

            // 1. Kiem tra phan mo rong file (whitelist)
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

            // 2. Kiem tra dung luong toi da 2MB
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($ext, $allowed_exts)) {
                $error = 'Định dạng ảnh không hợp lệ! Chỉ chấp nhận file JPG, JPEG, PNG, WEBP.';
            } elseif ($file_size > $max_size) {
                $error = 'Dung lượng ảnh vượt quá giới hạn 2MB!';
            } else {
                // Dam bao thu muc uploads ton tai
                if (!is_dir(UPLOAD_PATH)) {
                    mkdir(UPLOAD_PATH, 0777, true);
                }

                // Dat ten file ngau nhien an toan de chong trung lap va chong thuc thi file doc
                $thumbnail_name = 'news_' . time() . '_' . uniqid() . '.' . $ext;
                $destination = UPLOAD_PATH . $thumbnail_name;

                if (!move_uploaded_file($file_tmp, $destination)) {
                    $error = 'Không thể lưu file ảnh lên máy chủ. Vui lòng thử lại!';
                }
            }
        }

        // Neu khong co loi upload thi tien hanh luu CSDL
        if (empty($error)) {
            $slug = make_slug($title);

            $sql = "INSERT INTO news (title, slug, summary, content, thumbnail, category_id, views, status, created_at) 
                    VALUES (:title, :slug, :summary, :content, :thumb, :cid, 0, :status, NOW())";
            $params = [
                'title' => $title,
                'slug' => $slug,
                'summary' => $summary,
                'content' => $content,
                'thumb' => $thumbnail_name,
                'cid' => $category_id,
                'status' => $status
            ];

            $success = Database::db_execute($sql, $params);
            if ($success) {
                Helper::redirect('index.php');
            } else {
                $error = 'Lỗi lưu bài viết vào cơ sở dữ liệu. Vui lòng thử lại!';
            }
        }
    }
}
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../index.php">Tổng quan</a></li>
            <li class="breadcrumb-item"><a href="index.php">Bài viết</a></li>
            <li class="breadcrumb-item active" aria-current="page">Đăng bài mới</li>
        </ol>
    </nav>
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary"></i> Đăng bài viết mới
    </h3>
</div>

<div class="row">
    <div class="col-lg-10">
        <div class="admin-card">
            <div class="admin-card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small mb-4">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= clean($error) ?>
                    </div>
                <?php endif; ?>

                <form action="add.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_news">

                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold small">Tiêu đề bài viết <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?= clean($title) ?>" placeholder="Nhập tiêu đề tin tức công nghệ" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-bold small">Chuyên mục <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">-- Chọn chuyên mục --</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($category_id == $c['id']) ? 'selected' : '' ?>>
                                            <?= clean($c['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-bold small">Trạng thái xuất bản</label>
                            <select name="status" id="status" class="form-select">
                                <option value="1" <?= ($status == 1) ? 'selected' : '' ?>>Xuất bản ngay (Hiển thị ra ngoài trang chủ)</option>
                                <option value="0" <?= ($status == 0) ? 'selected' : '' ?>>Bản nháp (Lưu tạm, chưa hiển thị)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail" class="form-label fw-bold small">Ảnh đại diện (Thumbnail)</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text small">Chấp nhận định dạng: JPG, JPEG, PNG, WEBP (tối đa 2MB).</div>
                    </div>

                    <div class="mb-3">
                        <label for="summary" class="form-label fw-bold small">Mô tả ngắn / Tóm tắt <span class="text-danger">*</span></label>
                        <textarea name="summary" id="summary" rows="3" class="form-control" 
                                  placeholder="Đoạn văn ngắn 1-3 câu tóm tắt nội dung chính..." required><?= clean($summary) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-bold small">Nội dung chi tiết bài viết <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="12" class="form-control" 
                                  placeholder="Nhập toàn bộ nội dung bài viết tin tức tại đây..." required><?= clean($content) ?></textarea>
                        <div class="form-text small">Hỗ trợ các đoạn văn bản xuống dòng tự nhiên.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-cloud-arrow-up"></i> Đăng bài viết
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="bi bi-x-circle"></i> Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
