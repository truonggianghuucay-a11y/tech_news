<?php
/**
 * TRANG TONG QUAN ADMIN (DASHBOARD) - TECHNEWS
 * Thong ke so luong bai viet, chuyen muc, luot xem va liet ke cac bai viet gan day
 */
$admin_page_title = 'Bảng điều khiển';
require_once __DIR__ . '/includes/header.php';

// 1. Thong ke so lieu tong quan
$total_news = (int)Database::db_get_value("SELECT COUNT(*) FROM news");
$total_published = (int)Database::db_get_value("SELECT COUNT(*) FROM news WHERE status = 1");
$total_categories = (int)Database::db_get_value("SELECT COUNT(*) FROM categories");
$total_views = (int)Database::db_get_value("SELECT SUM(views) FROM news");

// 2. Lay 5 bai viet moi nhat
$sql_recent_news = "SELECT n.*, c.name as category_name 
                    FROM news n 
                    JOIN categories c ON n.category_id = c.id 
                    ORDER BY n.id DESC 
                    LIMIT 5";
$recent_news = Database::db_get_list($sql_recent_news);
?>

<div class="mb-4">
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-speedometer2 text-primary"></i> Tổng quan hệ thống
    </h3>
    <p class="text-secondary small mb-0">Thống kê nhanh và các hoạt động biên tập nội dung gần đây</p>
</div>

<!-- 4 Card thong ke -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Tổng bài viết</div>
                <div class="stat-number"><?= number_format($total_news) ?></div>
                <div class="small text-success mt-1">
                    <i class="bi bi-check-circle"></i> Đã xuất bản: <?= number_format($total_published) ?>
                </div>
            </div>
            <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 fs-3">
                <i class="bi bi-newspaper"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Chuyên mục</div>
                <div class="stat-number"><?= number_format($total_categories) ?></div>
                <div class="small text-muted mt-1">Đang hoạt động</div>
            </div>
            <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 fs-3">
                <i class="bi bi-folder2-open"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Tổng lượt đọc</div>
                <div class="stat-number"><?= number_format($total_views) ?></div>
                <div class="small text-muted mt-1">Tất cả bài viết</div>
            </div>
            <div class="p-3 bg-info bg-opacity-10 text-info rounded-3 fs-3">
                <i class="bi bi-eye"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-label">Bản nháp</div>
                <div class="stat-number"><?= number_format($total_news - $total_published) ?></div>
                <div class="small text-warning mt-1">Chưa xuất bản</div>
            </div>
            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 fs-3">
                <i class="bi bi-file-earmark-text"></i>
            </div>
        </div>
    </div>
</div>

<!-- Nut thao tac nhanh -->
<div class="mb-4 d-flex gap-2">
    <a href="news/add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Đăng bài viết mới
    </a>
    <a href="categories/add.php" class="btn btn-outline-primary">
        <i class="bi bi-folder-plus"></i> Thêm chuyên mục mới
    </a>
</div>

<!-- Bang 5 bai viet gan nhat -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-clock-history text-primary"></i> Bài viết đăng gần đây
        </h5>
        <a href="news/index.php" class="btn btn-sm btn-link text-decoration-none">Xem tất cả bài viết &raquo;</a>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tiêu đề bài viết</th>
                        <th>Chuyên mục</th>
                        <th>Lượt xem</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end" style="width: 140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_news)): ?>
                        <?php foreach ($recent_news as $row): 
                            $thumb = (!empty($row['thumbnail']) && file_exists(UPLOAD_PATH . $row['thumbnail'])) 
                                ? $site_root . 'assets/uploads/' . clean($row['thumbnail']) 
                                : 'https://placehold.co/120x80/0f172a/ffffff?text=News';
                        ?>
                            <tr>
                                <td>
                                    <img src="<?= $thumb ?>" alt="thumb" class="table-thumbnail">
                                </td>
                                <td>
                                    <a href="<?= $site_root ?>detail.php?id=<?= $row['id'] ?>" target="_blank" class="fw-bold text-dark text-decoration-none d-block">
                                        <?= clean($row['title']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border">
                                        <?= clean($row['category_name']) ?>
                                    </span>
                                </td>
                                <td><i class="bi bi-eye"></i> <?= number_format($row['views']) ?></td>
                                <td>
                                    <?php if ($row['status'] == 1): ?>
                                        <span class="badge bg-success-subtle text-success border border-success">Xuất bản</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning">Bản nháp</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary small"><?= format_date($row['created_at']) ?></td>
                                <td class="text-end">
                                    <a href="news/edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <a href="news/delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" title="Xóa">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Chưa có bài viết nào trong hệ thống.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
