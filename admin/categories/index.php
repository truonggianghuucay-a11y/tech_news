<?php
/**
 * DANH SACH CHUYEN MUC - TECHNEWS ADMIN
 */
$admin_page_title = 'Quản lý chuyên mục';
require_once __DIR__ . '/../includes/header.php';

// Lay danh sach chuyen muc kem theo tong so bai viet cua tung chuyen muc
$sql = "SELECT c.*, COUNT(n.id) as total_news 
        FROM categories c 
        LEFT JOIN news n ON c.id = n.category_id 
        GROUP BY c.id, c.name, c.slug, c.description, c.created_at 
        ORDER BY c.id ASC";
$categories = Database::db_get_list($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-folder2-open text-primary"></i> Quản lý Chuyên mục
        </h3>
        <p class="text-secondary small mb-0">Danh sách các chuyên mục phân loại tin tức công nghệ</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Thêm chuyên mục mới
    </a>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <span class="fw-bold text-dark">Tổng cộng: <?= !empty($categories) ? count($categories) : 0 ?> chuyên mục</span>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Tên chuyên mục</th>
                        <th>Slug (Đường dẫn)</th>
                        <th>Mô tả</th>
                        <th class="text-center" style="width: 140px;">Số bài viết</th>
                        <th>Ngày tạo</th>
                        <th class="text-end" style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="fw-bold text-secondary"><?= $cat['id'] ?></td>
                                <td class="fw-bold text-dark">
                                    <a href="<?= $site_root ?>category.php?id=<?= $cat['id'] ?>" target="_blank" class="text-dark text-decoration-none">
                                        <?= clean($cat['name']) ?>
                                    </a>
                                </td>
                                <td><code><?= clean($cat['slug']) ?></code></td>
                                <td class="text-secondary small"><?= clean($cat['description']) ?: '—' ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill"><?= $cat['total_news'] ?></span>
                                </td>
                                <td class="text-secondary small"><?= format_date($cat['created_at']) ?></td>
                                <td class="text-end">
                                    <a href="edit.php?id=<?= $cat['id'] ?>" class="btn btn-outline-primary btn-sm" title="Sửa">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <a href="delete.php?id=<?= $cat['id'] ?>" class="btn btn-outline-danger btn-sm" title="Xóa">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có chuyên mục nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
