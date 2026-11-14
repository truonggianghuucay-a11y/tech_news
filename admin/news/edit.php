<?php
/**
 * CHINH SUA BAI VIET - TECHNEWS ADMIN
 */
$admin_page_title = 'Chỉnh sửa bài viết';
require_once __DIR__ . '/../includes/header.php';

$id = (int)Helper::input_value('id');
if ($id <= 0) {
    Helper::redirect('index.php');
}

// Lay du lieu hien tai cua bai viet
$news = Database::db_get_row("SELECT * FROM news WHERE id = :id", ['id' => $id]);
if (!$news) {
    Helper::redirect('index.php');
}

// Lay danh sach tat ca chuyen muc
$categories = Database::db_get_list("SELECT id, name FROM categories ORDER BY name ASC");

$error = '';
$title = $news['title'];
$category_id = $news['category_id'];
$summary = $news['summary'];
$content = $news['content'];
$status = $news['status'];
$current_thumbnail = $news['thumbnail'];

// Xu ly khi submit form POST
if (Helper::is_submit('edit_news')) {
    $title = trim(Helper::input_value('title'));
    $category_id = (int)Helper::input_value('category_id');
    $summary = trim(Helper::input_value('summary'));
    $content = trim(Helper::input_value('content'));
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if (empty($title)) {
        $error = 'Vui lòng nhập tiêu đề bài viết!';
    } elseif ($category_id <= 0) {
        $error = 'Vui lòng chọn chuyên mục!';
    } elseif (empty($summary)) {
        $error = 'Vui lòng nhập tóm tắt bài viết!';
    } elseif (empty($content)) {
        $error = 'Vui lòng nhập nội dung chi tiết bài viết!';
    } else {
        $new_thumbnail = $current_thumbnail;

        // Neu nguoi dung co chon tai len anh moi
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['thumbnail']['tmp_name'];
            $file_name = $_FILES['thumbnail']['name'];
            $file_size = $_FILES['thumbnail']['size'];

            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            $max_size = 2 * 1024 * 1024;

            if (!in_array($ext, $allowed_exts)) {
                $error = 'Định dạng ảnh không hợp lệ! Chỉ chấp nhận JPG, JPEG, PNG, WEBP.';
            } elseif ($file_size > $max_size) {
                $error = 'Dung lượng ảnh vượt quá 2MB!';
            } else {
                if (!is_dir(UPLOAD_PATH)) {
                    mkdir(UPLOAD_PATH, 0777, true);
                }

                $new_filename = 'news_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($file_tmp, UPLOAD_PATH . $new_filename)) {
                    // Xoa anh cu neu ton tai de tiet kiem dung luong
                    if (!empty($current_thumbnail) && file_exists(UPLOAD_PATH . $current_thumbnail)) {
                        @unlink(UPLOAD_PATH . $current_thumbnail);
                    }
                    $new_thumbnail = $new_filename;
                } else {
                    $error = 'Không thể lưu file ảnh mới!';
                }
            }
        }

        if (empty($error)) {
            $slug = make_slug($title);

            $sql = "UPDATE news 
                    SET title = :title, slug = :slug, summary = :summary, 
                        content = :content, thumbnail = :thumb, category_id = :cid, 
                        status = :status, updated_at = NOW() 
                    WHERE id = :id";
            $params = [
                'title' => $title,
                'slug' => $slug,
                'summary' => $summary,
                'content' => $content,
                'thumb' => $new_thumbnail,
                'cid' => $category_id,
                'status' => $status,
                'id' => $id
            ];

            Database::db_execute($sql, $params);
            Helper::redirect('index.php');
        }
    }
}
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../index.php">Tổng quan</a></li>
            <li class="breadcrumb-item"><a href="index.php">Bài viết</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary"></i> Chỉnh sửa bài viết
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

                <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_news">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold small">Tiêu đề bài viết <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?= clean($title) ?>" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-bold small">Chuyên mục <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select" required>
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
                                <option value="1" <?= ($status == 1) ? 'selected' : '' ?>>Xuất bản ngay</option>
                                <option value="0" <?= ($status == 0) ? 'selected' : '' ?>>Bản nháp</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="thumbnail" class="form-label fw-bold small">Ảnh đại diện mới (Bỏ qua nếu giữ ảnh cũ)</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        
                        <?php if (!empty($current_thumbnail)): ?>
                            <div class="mt-2 d-flex align-items-center gap-3 p-2 bg-light rounded border">
                                <span class="small text-secondary">Ảnh hiện tại:</span>
                                <img src="<?= $site_root ?>assets/uploads/<?= clean($current_thumbnail) ?>" alt="thumb" style="height: 50px; border-radius: 4px;">
                                <span class="small text-muted font-monospace"><?= clean($current_thumbnail) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="summary" class="form-label fw-bold small">Mô tả ngắn / Tóm tắt <span class="text-danger">*</span></label>
                        <textarea name="summary" id="summary" rows="3" class="form-control" required><?= clean($summary) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-bold small">Nội dung chi tiết bài viết <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="12" class="form-control" required><?= clean($content) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle"></i> Cập nhật bài viết
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
