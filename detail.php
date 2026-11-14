<?php
/**
 * TRANG CHI TIET BAI VIET - TECHNEWS
 * Hien thi noi dung day du, tu dong tang luot xem va goi y bai viet lien quan
 */
require_once __DIR__ . '/config.php';

// 1. Lay ID bai viet tu URL
$id = (int)Helper::input_value('id');
if ($id <= 0) {
    Helper::redirect('index.php');
}

// 2. Lay thong tin chi tiet bai viet
$sql_post = "SELECT n.*, c.name as category_name 
             FROM news n 
             JOIN categories c ON n.category_id = c.id 
             WHERE n.id = :id AND n.status = 1";
$post = Database::db_get_row($sql_post, ['id' => $id]);

if (!$post) {
    Helper::redirect('index.php');
}

// 3. Tu dong tang luot xem (views = views + 1)
Database::db_execute("UPDATE news SET views = views + 1 WHERE id = :id", ['id' => $id]);
// Cap nhat luot xem ngay tren bien $post de hien thi ngay
$post['views'] += 1;

$page_title = $post['title'];

// 4. Lay danh sach bai viet lien quan (cung category_id, khac id hien tai)
$sql_related = "SELECT id, title, thumbnail, created_at, views 
                FROM news 
                WHERE category_id = :cid AND id != :current_id AND status = 1 
                ORDER BY id DESC 
                LIMIT 3";
$related_news = Database::db_get_list_condition($sql_related, [
    'cid' => $post['category_id'],
    'current_id' => $id
]);

// Nap giao dien
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="category.php?id=<?= $post['category_id'] ?>" class="text-decoration-none"><?= clean($post['category_name']) ?></a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width: 350px;" aria-current="page"><?= clean($post['title']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Cot noi dung chi tiet -->
        <main class="col-lg-8">
            <article class="bg-white p-4 p-md-5 rounded-3 border">
                <!-- Tieu de & Thong tin metadata -->
                <header class="article-header">
                    <a href="category.php?id=<?= $post['category_id'] ?>" class="badge bg-primary text-decoration-none mb-2">
                        <?= clean($post['category_name']) ?>
                    </a>
                    <h1 class="article-title"><?= clean($post['title']) ?></h1>
                    
                    <div class="d-flex flex-wrap align-items-center text-muted small mt-3 gap-3">
                        <span><i class="bi bi-calendar3"></i> Đăng ngày: <?= format_date($post['created_at']) ?></span>
                        <span><i class="bi bi-eye"></i> <?= number_format($post['views']) ?> lượt xem</span>
                        <?php if (!empty($post['updated_at']) && $post['updated_at'] != $post['created_at']): ?>
                            <span><i class="bi bi-arrow-repeat"></i> Cập nhật: <?= format_date($post['updated_at']) ?></span>
                        <?php endif; ?>
                    </div>
                </header>

                <!-- Tom tat / Mo ta ngan -->
                <div class="article-summary">
                    <?= clean($post['summary']) ?>
                </div>

                <!-- Anh dai dien chinh (neu co) -->
                <?php if (!empty($post['thumbnail'])): 
                    $main_thumb = file_exists(UPLOAD_PATH . $post['thumbnail']) 
                        ? 'assets/uploads/' . clean($post['thumbnail']) 
                        : 'https://placehold.co/900x500/0f172a/ffffff?text=' . urlencode(mb_substr($post['title'], 0, 20));
                ?>
                    <div class="mb-4 text-center">
                        <img src="<?= $main_thumb ?>" alt="<?= clean($post['title']) ?>" class="article-thumbnail-main img-fluid">
                    </div>
                <?php endif; ?>

                <!-- Noi dung chi tiet bai viet -->
                <div class="article-content">
                    <?= nl2br($post['content']) ?>
                </div>

                <!-- Nut chia se / tro ve -->
                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="category.php?id=<?= $post['category_id'] ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Xem thêm tin cùng chuyên mục
                    </a>
                    <a href="index.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-house"></i> Về trang chủ
                    </a>
                </div>
            </article>

            <!-- Bai viet lien quan -->
            <?php if (!empty($related_news)): ?>
                <div class="mt-4 pt-2">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-grid-fill text-primary"></i> Bài viết liên quan
                    </h5>
                    <div class="row g-3">
                        <?php foreach ($related_news as $rel): 
                            $rel_thumb = (!empty($rel['thumbnail']) && file_exists(UPLOAD_PATH . $rel['thumbnail'])) 
                                ? 'assets/uploads/' . clean($rel['thumbnail']) 
                                : 'https://placehold.co/600x400/1e293b/ffffff?text=TechNews';
                        ?>
                            <div class="col-md-4">
                                <div class="news-card">
                                    <div class="news-card-img-wrapper" style="height: 140px;">
                                        <a href="detail.php?id=<?= $rel['id'] ?>">
                                            <img src="<?= $rel_thumb ?>" alt="<?= clean($rel['title']) ?>" class="news-card-img">
                                        </a>
                                    </div>
                                    <div class="news-card-body p-3">
                                        <h6 class="mb-1">
                                            <a href="detail.php?id=<?= $rel['id'] ?>" class="news-card-title fs-6">
                                                <?= clean($rel['title']) ?>
                                            </a>
                                        </h6>
                                        <div class="news-card-meta small text-muted pt-2 mt-auto">
                                            <span><?= date('d/m/Y', strtotime($rel['created_at'])) ?></span>
                                            <span><i class="bi bi-eye"></i> <?= number_format($rel['views']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>

        <!-- Sidebar cot phai -->
        <div class="col-lg-4">
            <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
