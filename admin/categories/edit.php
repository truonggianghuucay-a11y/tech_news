<?php
/**
 * CHINH SUA CHUYEN MUC - TECHNEWS ADMIN
 */
$admin_page_title = 'Chỉnh sửa chuyên mục';
require_once __DIR__ . '/../includes/header.php';

$id = (int)Helper::input_value('id');
if ($id <= 0) {
    Helper::redirect('index.php');
}

// Lay du lieu hien tai cua chuyen muc
$category = Database::db_get_row("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
if (!$category) {
    Helper::redirect('index.php');
}

$error = '';
$name = $category['name'];
$slug = $category['slug'];
$description = $category['description'];

// Xu ly khi submit form POST
if (Helper::is_submit('edit_category')) {
    $name = trim(Helper::input_value('name'));
    $slug = trim(Helper::input_value('slug'));
    $description = trim(Helper::input_value('description'));

    if (empty($name)) {
        $error = 'Vui lòng nhập tên chuyên mục!';
    } else {
        if (empty($slug)) {
            $slug = make_slug($name);
        } else {
            $slug = make_slug($slug);
        }

        // Kiem tra trung lap slug (loai tru chinh ban ghi hien tai)
        $check = Database::db_get_row("SELECT id FROM categories WHERE slug = :slug AND id != :id", [
            'slug' => $slug,
            'id' => $id
        ]);

        if ($check) {
            $error = 'Đường dẫn (slug) này đã bị trùng với chuyên mục khác!';
        } else {
            // Cap nhat CSDL
            $sql = "UPDATE categories SET name = :name, slug = :slug, description = :description WHERE id = :id";
            $success = Database::db_execute($sql, [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'id' => $id
            ]);

            // Du lieu co the khong doi (rowCount = 0), van chuyen huong ve danh sach
            Helper::redirect('index.php');
        }
    }
}
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../index.php">Tổng quan</a></li>
            <li class="breadcrumb-item"><a href="index.php">Chuyên mục</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square text-primary"></i> Chỉnh sửa chuyên mục
    </h3>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="admin-card">
            <div class="admin-card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small mb-4">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= clean($error) ?>
                    </div>
                <?php endif; ?>

                <form action="edit.php?id=<?= $id ?>" method="POST">
                    <input type="hidden" name="action" value="edit_category">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small">Tên chuyên mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="<?= clean($name) ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-bold small">Đường dẫn thân thiện (Slug)</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="<?= clean($slug) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold small">Mô tả ngắn</label>
                        <textarea name="description" id="description" rows="3" class="form-control"><?= clean($description) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle"></i> Cập nhật thay đổi
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
