<?php
/**
 * TRANG CHUYEN MUC - TECHNEWS
 * Hien thi danh sach tin thuoc mot chuyen muc cu the, kem phan trang
 */
require_once __DIR__ . '/config.php';

// 1. Lay ma chuyen muc tu URL
$category_id = (int)Helper::input_value('id');
if ($category_id <= 0) {
    Helper::redirect('index.php');
}

// 2. Lay thong tin chuyen muc
$category = Database::db_get_row("SELECT * FROM categories WHERE id = :id", ['id' => $category_id]);
if (!$category) {
    Helper::redirect('index.php');
}

$page_title = $category['name'] . ' - Tin tức công nghệ';

// 3. Dem tong so bai viet thuoc chuyen muc nay
$sql_count = "SELECT COUNT(*) FROM news WHERE category_id = :cid AND status = 1";
$total_records = Database::db_get_value($sql_count, ['cid' => $category_id]);

// 4. Xu ly phan trang bang ham paging()
$limit = 6;
$current_page = (int)Helper::input_value('trang');
if ($current_page < 1) {
    $current_page = 1;
}

$link = "category.php?id={$category_id}&trang={page}";
$pg = paging($link, $total_records, $current_page, $limit);

// 5. Lay danh sach bai viet thuoc chuyen muc theo trang
$sql_news = "SELECT * FROM news 
             WHERE category_id = :cid AND status = 1 
             ORDER BY id DESC 
             LIMIT {$pg['start']}, {$pg['limit']}";
$news_list = Database::db_get_list_condition($sql_news, ['cid' => $category_id]);

// Nap giao dien
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-4">
    <!-- Tieu de chuyen muc -->
    <div class="bg-white p-4 rounded-3 border mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= clean($category['name']) ?></li>
            </ol>
        </nav>
        <h2 class="fw-bold text-dark mb-1">
            <i class="bi bi-folder2-open text-primary"></i> <?= clean($category['name']) ?>
        </h2>
        <?php if (!empty($category['description'])): ?>
            <p class="text-muted mb-0"><?= clean($category['description']) ?></p>
        <?php endif; ?>
    </div>

    <!-- Danh sach tin va Sidebar -->
    <div class="row g-4">
        <main class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <span class="fw-semibold text-secondary">
                    Tìm thấy <span class="text-dark fw-bold"><?= number_format($total_records) ?></span> bài viết
                </span>
            </div>

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
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Chưa có bài viết nào trong chuyên mục này.
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
