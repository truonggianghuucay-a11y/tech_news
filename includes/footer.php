<?php
/**
 * Footer dung chung - 100% Khong co the <script> nao
 */
?>
<footer class="tech-footer">
    <div class="container">
        <div class="row g-4">
            <!-- Cot 1: Gioi thieu -->
            <div class="col-lg-5 col-md-6">
                <a href="index.php" class="brand-logo mb-3 d-inline-block">
                    TECH<span>NEWS</span>
                </a>
                <p class="text-secondary small">
                    Trang tin tức công nghệ tổng hợp, cập nhật liên tục 24/7 về các xu hướng công nghệ mới nhất: Trí tuệ nhân tạo (AI), điện thoại thông minh, máy tính xách tay, linh kiện phần cứng và phong cách sống số.
                </p>
                <div class="text-secondary small">
                    <p class="mb-1"><i class="bi bi-geo-alt"></i> Hà Nội, Việt Nam</p>
                    <p class="mb-1"><i class="bi bi-envelope"></i> contact@technews.vn</p>
                </div>
            </div>

            <!-- Cot 2: Chuyen muc nhanh -->
            <div class="col-lg-3 col-md-6">
                <h5>Chuyên mục chính</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="index.php"><i class="bi bi-chevron-right"></i> Trang chủ</a></li>
                    <li class="mb-2"><a href="category.php?id=1"><i class="bi bi-chevron-right"></i> Trí tuệ nhân tạo (AI)</a></li>
                    <li class="mb-2"><a href="category.php?id=2"><i class="bi bi-chevron-right"></i> Điện thoại & Di động</a></li>
                    <li class="mb-2"><a href="category.php?id=3"><i class="bi bi-chevron-right"></i> Máy tính & Phần cứng</a></li>
                    <li class="mb-2"><a href="category.php?id=4"><i class="bi bi-chevron-right"></i> Phần mềm & Ứng dụng</a></li>
                </ul>
            </div>

            <!-- Cot 3: Thong tin & Lien he -->
            <div class="col-lg-4 col-md-12">
                <h5>Về TechNews</h5>
                <p class="text-secondary small">
                    Hệ thống website được xây dựng bằng PHP thuần, MySQL và Bootstrap 5 (CSS Only), đề cao tốc độ tải trang, tính ổn định và bảo mật dữ liệu.
                </p>
                <div class="mt-3">
                    <a href="admin/login.php" class="btn btn-sm btn-outline-secondary text-light">
                        <i class="bi bi-shield-lock"></i> Khu vực Quản trị viên
                    </a>
                </div>
            </div>
        </div>

        <!-- Ban quyen -->
        <div class="footer-bottom text-center text-secondary">
            <p class="mb-0">
                &copy; <?= date('Y') ?> TechNews. Bản quyền thuộc về TechNews. Xây dựng bằng PHP thuần & Bootstrap 5.
            </p>
        </div>
    </div>
</footer>

</body>
</html>
