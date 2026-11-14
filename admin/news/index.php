<?php
/**
 * QUAN LY BAI VIET TIN TUC - TECHNEWS ADMIN
 * Liet ke bai viet, loc theo chuyen muc bang GET va phan trang bang paging()
 */
$admin_page_title = 'Quản lý bài viết';
require_once __DIR__ . '/../includes/header.php';

// 1. Lay danh sach chuyen muc de phuc vu bo loc
$categories = Database::db_get_list("SELECT id, name FROM categories ORDER BY name ASC");

// 2. Xu ly bo loc theo chuyen muc qua GET
$filter_category_id = (int)Helper::input_value('category_id');

// 3. Xay dung cau truy van dem va danh sach
$where = "WHERE 1=1";
$params = [];

if ($filter_category_id > 0) {
    $where .= " AND n.category_id = :cid";
    $params['cid'] = $filter_category_id;
}

// 4. Dem tong so ban ghi
$sql_count = "SELECT COUNT(*) FROM news n {$where}";
$total_records = Database::db_get_value($sql_count, $params);

// 5. Xu ly phan trang bang ham paging()
$limit = 8;
$current_page = (int)Helper::input_value('trang');
if ($current_page < 1) {
    $current_page = 1;
}

$link_param = "index.php?" . ($filter_category_id > 0 ? "category_id={$filter_category_id}&" : "") . "trang={page}";
$pg = paging($link_param, $total_records, $current_page, $limit);

// 6. Truy van danh sach bai viet theo trang
$sql_list = "SELECT n.*, c.name as category_name 
             FROM news n 
             JOIN categories c ON n.category_id = c.id 
             {$where} 
             ORDER BY n.id DESC 
             LIMIT {$pg['start']}, {$pg['limit']}";
$news_list = Database::db_get_list_condition($sql_list, $params);
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-newspaper text-primary"></i> Quản lý Bài viết
        </h3>
        <p class="text-secondary small mb-0">Danh sách tất cả các bài viết tin tức công nghệ</p>
    </div>
    <a href="add.php" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Đăng bài viết mới
    </a>
</div>

<!-- Bo loc theo chuyen muc (Form GET thuan) -->
<div class="admin-card mb-4">
    <div class="admin-card-body py-3">
        <form action="index.php" method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="category_id" class="col-form-label fw-bold small text-secondary">Lọc theo chuyên mục:</label>
            </div>
            <div class="col-auto">
                <select name="category_id" id="category_id" class="form-select form-select-sm">
                    <option value="0">-- Tất cả chuyên mục --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($filter_category_id == $c['id']) ? 'selected' : '' ?>>
                                <?= clean($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel"></i> Lọc dữ liệu
                </button>
                <?php if ($filter_category_id > 0): ?>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i> Bỏ lọc
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Bang danh sach bai viet -->
<div class="admin-card">
    <div class="admin-card-header">
        <span class="fw-bold text-dark">Tìm thấy: <?= number_format($total_records) ?> bài viết</span>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tiêu đề bài viết</th>
                        <th>Chuyên mục</th>
                        <th>Lượt đọc</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end" style="width: 140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($news_list)): ?>
                        <?php foreach ($news_list as $row): 
                            $thumb = (!empty($row['thumbnail']) && file_exists(UPLOAD_PATH . $row['thumbnail'])) 
                                ? $site_root . 'assets/uploads/' . clean($row['thumbnail']) 
                                : 'https://placehold.co/120x80/0f172a/ffffff?text=Tech';
                        ?>
                            <tr>
                                <td class="fw-bold text-secondary"><?= $row['id'] ?></td>
                                <td>
                                    <img src="<?= $thumb ?>" alt="thumb" class="table-thumbnail">
                                </td>
                                <td>
                                    <a href="<?= $site_root ?>detail.php?id=<?= $row['id'] ?>" target="_blank" class="fw-bold text-dark text-decoration-none">
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
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm" title="Sửa">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" title="Xóa">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Không có bài viết nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Phan trang bootstrap 5 -->
    <?php if ($total_records > $limit): ?>
        <div class="p-3 border-top">
            <?= $pg['html'] ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
