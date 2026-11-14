<?php
/**
 * TRANG CHU - TECHNEWS
 * Hien thi tin noi bat (Hero story) va danh sach tin moi co phan trang bang ham paging()
 */
require_once __DIR__ . '/config.php';

$page_title = 'Trang chủ - Tin tức công nghệ mới nhất';

// 1. Lay 1 bai viet noi bat nhat (moi nhat) lam Hero Story
$featured_post = Database::db_get_row("SELECT n.*, c.name as category_name 
                                       FROM news n 
                                       JOIN categories c ON n.category_id = c.id 
                                       WHERE n.status = 1 
                                       ORDER BY n.id DESC 
                                       LIMIT 1");

$featured_id = $featured_post ? (int)$featured_post['id'] : 0;

// 2. Tinh tong so bai viet (loai tru bai viet featured o tren de khong bi trung lap)
$sql_count = "SELECT COUNT(*) FROM news WHERE status = 1 AND id != :fid";
$total_records = Database::db_get_value($sql_count, ['fid' => $featured_id]);

// 3. Xu ly phan trang bang ham paging() co san trong lib.php
$limit = 6; // 6 bai viet tren moi trang
$current_page = (int)Helper::input_value('trang');
if ($current_page < 1) {
    $current_page = 1;
}

$pg = paging('index.php?trang={page}', $total_records, $current_page, $limit);

// 4. Lay danh sach bai viet theo trang
$sql_news = "SELECT n.*, c.name as category_name 
             FROM news n 
             JOIN categories c ON n.category_id = c.id 
             WHERE n.status = 1 AND n.id != :fid 
             ORDER BY n.id DESC 
             LIMIT {$pg['start']}, {$pg['limit']}";
$news_list = Database::db_get_list_condition($sql_news, ['fid' => $featured_id]);

// Nap giao dien
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-4">
    <!-- Bai viet noi bat (Hero Feature) chi hien o trang 1 -->
    <?php if ($current_page == 1 && $featured_post): 
        $hero_img = (!empty($featured_post['thumbnail']) && file_exists(UPLOAD_PATH . $featured_post['thumbnail'])) 
            ? 'assets/uploads/' . clean($featured_post['thumbnail']) 
            : 'https://placehold.co/900x500/0f172a/ffffff?text=' . urlencode(mb_substr($featured_post['title'], 0, 25));
    ?>
        <div class="featured-card">
            <div class="row g-0">
                <div class="col-lg-7">
                    <div class="featured-img-wrapper">
                        <img src="<?= $hero_img ?>" alt="<?= clean($featured_post['title']) ?>" class="featured-img">
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center">
                    <div class="p-4 p-lg-5">
                        <span class="badge bg-primary mb-2"><?= clean($featured_post['category_name']) ?></span>
                        <h2 class="mb-3">
                            <a href="detail.php?id=<?= $featured_post['id'] ?>" class="featured-title">
                                <?= clean($featured_post['title']) ?>
                            </a>
                        </h2>
                        <p class="text-muted mb-4"><?= clean($featured_post['summary']) ?></p>
                        <div class="d-flex align-items-center text-muted small">
                            <span class="me-3"><i class="bi bi-clock"></i> <?= format_date($featured_post['created_at']) ?></span>
                            <span><i class="bi bi-eye"></i> <?= number_format($featured_post['views']) ?> lượt xem</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Khu vuc chinh: Danh sach tin + Sidebar -->
    <div class="row g-4">
        <!-- Cot noi dung chinh -->
        <main class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-newspaper text-primary"></i> Tin tức mới nhất
                </h4>
                <span class="text-muted small">Tổng cộng <?= number_format($total_records) ?> bài viết</span>
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

                <!-- Phan trang Bootstrap 5 sinh boi ham paging() -->
                <div class="mt-4 pt-2">
                    <?= $pg['html'] ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Chưa có bài viết nào được đăng tải.
                </div>
            <?php endif; ?>
        </main>

        <!-- Cot Sidebar -->
        <div class="col-lg-4">
            <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
