<?php
/**
 * TRANG TIM KIEM TIN TUC - TECHNEWS
 * Xu ly tim kiem thuan GET + Prepared Statements + Phan trang bang paging()
 */
require_once __DIR__ . '/config.php';

$keyword = trim(Helper::input_value('keyword'));
$page_title = 'Kết quả tìm kiếm: ' . (!empty($keyword) ? $keyword : 'Tất cả bài viết');

$total_records = 0;
$news_list = [];
$pg = ['html' => '', 'start' => 0, 'limit' => 6];

if (!empty($keyword)) {
    // 1. Dem tong so ket qua phu hop
    $sql_count = "SELECT COUNT(*) FROM news 
                  WHERE status = 1 
                  AND (title LIKE :kw1 OR summary LIKE :kw2 OR content LIKE :kw3)";
    $search_param = '%' . $keyword . '%';
    $total_records = Database::db_get_value($sql_count, [
        'kw1' => $search_param,
        'kw2' => $search_param,
        'kw3' => $search_param
    ]);

    // 2. Xu ly phan trang bang ham paging()
    $limit = 6;
    $current_page = (int)Helper::input_value('trang');
    if ($current_page < 1) {
        $current_page = 1;
    }

    $link = "search.php?keyword=" . urlencode($keyword) . "&trang={page}";
    $pg = paging($link, $total_records, $current_page, $limit);

    // 3. Lay danh sach bai viet tim duoc theo trang
    $sql_news = "SELECT n.*, c.name as category_name 
                 FROM news n 
                 JOIN categories c ON n.category_id = c.id 
                 WHERE n.status = 1 
                 AND (n.title LIKE :kw1 OR n.summary LIKE :kw2 OR n.content LIKE :kw3) 
                 ORDER BY n.id DESC 
                 LIMIT {$pg['start']}, {$pg['limit']}";
    $news_list = Database::db_get_list_condition($sql_news, [
        'kw1' => $search_param,
        'kw2' => $search_param,
        'kw3' => $search_param
    ]);
}

// Nap giao dien
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-4">
    <!-- Thong tin ket qua tim kiem -->
    <div class="bg-white p-4 rounded-3 border mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tìm kiếm</li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-search text-primary"></i> Kết quả tìm kiếm cho: "<?= clean($keyword) ?>"
        </h2>
        <p class="text-muted mb-0">
            Tìm thấy <strong class="text-dark"><?= number_format($total_records) ?></strong> bài viết phù hợp.
        </p>
    </div>

    <!-- Danh sach ket qua va Sidebar -->
    <div class="row g-4">
        <main class="col-lg-8">
            <?php if (!empty($keyword)): ?>
                <?php if (!empty($news_list)): ?>
                    <div class="row g-4">
                        <?php foreach ($news_list as $news): 
                            $thumb = (!empty($news['thumbnail']) && file_exists(UPLOAD_PATH . $news['thumbnail'])) 
                                ? 'assets/uploads/' . clean($news['thumbnail']) 
                                : 'https://placehold.co/600x400/1e293b/ffffff?text=TechNews';
                        ?>
                            <div class="col-md-6">
                                <article class="news-card">
                                    <div class="news-card-img-wrapper">
                                        <a href="detail.php?id=<?= $news['id'] ?>">
                                            <img src="<?= $thumb ?>" alt="<?= clean($news['title']) ?>" class="news-card-img">
                                        </a>
                                    </div>
                                    <div class="news-card-body">
                                        <div class="mb-2">
                                            <a href="category.php?id=<?= $news['category_id'] ?>" class="badge bg-light text-primary text-decoration-none">
                                                <?= clean($news['category_name']) ?>
                                            </a>
                                        </div>
                                        <h5>
                                            <a href="detail.php?id=<?= $news['id'] ?>" class="news-card-title">
                                                <?= clean($news['title']) ?>
                                            </a>
                                        </h5>
                                        <p class="news-card-summary">
                                            <?= clean($news['summary']) ?>
                                        </p>
                                        <div class="news-card-meta">
                                            <span><i class="bi bi-calendar3"></i> <?= date('d/m/Y', strtotime($news['created_at'])) ?></span>
                                            <span><i class="bi bi-eye"></i> <?= number_format($news['views']) ?></span>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Phan trang -->
                    <div class="mt-4 pt-2">
                        <?= $pg['html'] ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Không tìm thấy bài viết nào chứa từ khóa "<strong><?= clean($keyword) ?></strong>". Vui lòng thử lại với từ khóa khác!
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Vui lòng nhập từ khóa tìm kiếm vào ô phía trên.
                </div>
            <?php endif; ?>
        </main>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
