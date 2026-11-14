<?php
/**
 * THEM CHUYEN MUC MOI - TECHNEWS ADMIN
 */
$admin_page_title = 'Thêm chuyên mục mới';
require_once __DIR__ . '/../includes/header.php';

$error = '';
$name = '';
$slug = '';
$description = '';

// Xu ly Submit Form qua phuong thuc POST
if (Helper::is_submit('add_category')) {
    $name = trim(Helper::input_value('name'));
    $slug = trim(Helper::input_value('slug'));
    $description = trim(Helper::input_value('description'));

    if (empty($name)) {
        $error = 'Vui lòng nhập tên chuyên mục!';
    } else {
        // Neu khong nhap slug thi tu dong tao slug tu ten
        if (empty($slug)) {
            $slug = make_slug($name);
        } else {
            $slug = make_slug($slug);
        }

        // Kiem tra xem slug da ton tai chua
        $check = Database::db_get_row("SELECT id FROM categories WHERE slug = :slug", ['slug' => $slug]);
        if ($check) {
            $error = 'Đường dẫn (slug) này đã tồn tại! Vui lòng chọn tên hoặc slug khác.';
        } else {
            // Them vao CSDL bang Prepared Statement
            $sql = "INSERT INTO categories (name, slug, description, created_at) 
                    VALUES (:name, :slug, :description, NOW())";
            $success = Database::db_execute($sql, [
                'name' => $name,
                'slug' => $slug,
                'description' => $description
            ]);

            if ($success) {
                Helper::redirect('index.php');
            } else {
                $error = 'Có lỗi xảy ra khi lưu dữ liệu. Vui lòng thử lại!';
            }
        }
    }
}
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../index.php">Tổng quan</a></li>
            <li class="breadcrumb-item"><a href="index.php">Chuyên mục</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
        </ol>
    </nav>
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-folder-plus text-primary"></i> Thêm chuyên mục mới
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

                <form action="add.php" method="POST">
                    <input type="hidden" name="action" value="add_category">

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small">Tên chuyên mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" 
                               value="<?= clean($name) ?>" placeholder="Ví dụ: Trí tuệ nhân tạo (AI)" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-bold small">Đường dẫn thân thiện (Slug)</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="<?= clean($slug) ?>" placeholder="Để trống nếu muốn tự tạo từ tên chuyên mục">
                        <div class="form-text small">Ví dụ: tri-tue-nhan-tao-ai (chỉ chứa chữ thường không dấu và dấu gạch ngang)</div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold small">Mô tả ngắn</label>
                        <textarea name="description" id="description" rows="3" class="form-control" 
                                  placeholder="Nhập mô tả ngắn về chuyên mục này..."><?= clean($description) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check2-circle"></i> Lưu chuyên mục
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
