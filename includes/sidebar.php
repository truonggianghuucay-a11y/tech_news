<?php
/**
 * Sidebar cot ben phai - Tin xem nhieu, Tin moi, Danh muc
 */

// 1. Lay top 5 tin xem nhieu nhat
$sql_popular = "SELECT id, title, thumbnail, views, created_at 
                FROM news 
                WHERE status = 1 
                ORDER BY views DESC 
                LIMIT 5";
$popular_news = Database::db_get_list($sql_popular);

// 2. Lay danh sach chuyen muc kem so luong bai viet
$sql_cat_counts = "SELECT c.id, c.name, COUNT(n.id) as total_posts 
                   FROM categories c 
                   LEFT JOIN news n ON c.id = n.category_id AND n.status = 1 
                   GROUP BY c.id, c.name 
                   ORDER BY c.id ASC";
$cat_list_counts = Database::db_get_list($sql_cat_counts);
?>

<aside class="tech-sidebar">
    <!-- Box: Tin xem nhieu nhat -->
    <div class="sidebar-box">
        <h5 class="sidebar-title">
            <i class="bi bi-fire text-danger"></i> Đọc nhiều nhất
        </h5>
        <?php if (!empty($popular_news)): ?>
            <?php foreach ($popular_news as $item): 
                $img_src = (!empty($item['thumbnail']) && file_exists(UPLOAD_PATH . $item['thumbnail'])) 
                    ? 'assets/uploads/' . clean($item['thumbnail']) 
                    : 'https://placehold.co/120x90/0f172a/ffffff?text=Tech';
            ?>
                <a href="detail.php?id=<?= $item['id'] ?>" class="sidebar-item">
                    <img src="<?= $img_src ?>" alt="<?= clean($item['title']) ?>" class="sidebar-item-img">
                    <div>
                        <div class="sidebar-item-title"><?= clean($item['title']) ?></div>
                        <div class="sidebar-item-meta">
                            <span><i class="bi bi-eye"></i> <?= number_format($item['views']) ?></span>
                            <span class="ms-2"><i class="bi bi-clock"></i> <?= date('d/m', strtotime($item['created_at'])) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted small mb-0">Chưa có bài viết.</p>
        <?php endif; ?>
    </div>

    <!-- Box: Chuyen muc tin tuc -->
    <div class="sidebar-box">
        <h5 class="sidebar-title">
            <i class="bi bi-tags text-primary"></i> Chuyên mục
        </h5>
        <div class="list-group list-group-flush">
            <?php if (!empty($cat_list_counts)): ?>
                <?php foreach ($cat_list_counts as $c): ?>
                    <a href="category.php?id=<?= $c['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2 border-0 border-bottom">
                        <span><?= clean($c['name']) ?></span>
                        <span class="badge bg-light text-dark rounded-pill"><?= $c['total_posts'] ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</aside>
