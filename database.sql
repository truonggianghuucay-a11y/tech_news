-- ============================================================
--  CO SO DU LIEU: tech_news
--  WEBSITE TIN TUC CONG NGHE - TECHNEWS
-- ------------------------------------------------------------
--  Engine  : MariaDB 10.4+ / MySQL 8.0+
--  Charset : utf8mb4 (ho tro tieng Viet day du, emoji)
--  Collate : utf8mb4_unicode_ci (thong nhat tren database VA tung bang)
-- ------------------------------------------------------------
--  HUONG DAN IMPORT:
--    phpMyAdmin : Database > Import > chon file nay > Go
--    Command    : mysql -u root < database.sql
-- ============================================================

-- Xoa va tao lai database sach (bo comment dong nay neu da co du lieu)
-- DROP DATABASE IF EXISTS `tech_news`;

CREATE DATABASE IF NOT EXISTS `tech_news`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `tech_news`;

-- Sua collation database cho khop voi cac bang (MariaDB 10.4)
ALTER DATABASE `tech_news`
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

-- Tat kiem tra khoa ngoai trong qua trinh import de tranh loi thu tu
SET FOREIGN_KEY_CHECKS = 0;


-- ============================================================
--  BANG 1: admins - Tai khoan quan tri vien
-- ============================================================
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(50)      NOT NULL,
    `password`   VARCHAR(255)     NOT NULL COMMENT 'Bam bang password_hash(PASSWORD_DEFAULT)',
    `fullname`   VARCHAR(100)     NOT NULL,
    `email`      VARCHAR(150)     NULL     DEFAULT NULL,
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_username` (`username`),
    UNIQUE KEY `uq_email`    (`email`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tai khoan quan tri vien cua he thong TechNews';

-- Du lieu mau: admin / admin123  (hash boi password_hash boi PHP XAMPP 10.4)
INSERT INTO `admins` (`id`, `username`, `password`, `fullname`, `email`) VALUES
(1, 'admin', '$2y$10$egR8oVVP9EqVU.1m/xAH2uZ05fqc/Vvejq0Q8oC3MYMr2HWaZOV/i', 'Quản trị viên TechNews', 'admin@technews.vn');


-- ============================================================
--  BANG 2: categories - Danh muc tin tuc
-- ============================================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)     NOT NULL,
    `slug`        VARCHAR(100)     NOT NULL COMMENT 'Duong dan than thien, chi chu thuong khong dau va gach ngang',
    `description` VARCHAR(500)     NULL     DEFAULT NULL COMMENT 'Mo ta ngan ve chuyen muc',
    `sort_order`  TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Thu tu hien thi, so nho hien truoc',
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_slug` (`slug`),
    KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Danh muc tin tuc cong nghe';

-- Du lieu mau 5 chuyen muc tin tuc cong nghe
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `sort_order`) VALUES
(1, 'Trí tuệ nhân tạo (AI)',   'tri-tue-nhan-tao-ai',  'Cập nhật tin tức về các mô hình AI, học máy, học sâu và xu hướng trí tuệ nhân tạo mới nhất trên thế giới', 1),
(2, 'Điện thoại & Di động',    'dien-thoai-di-dong',   'Tin mới nhất về smartphone, iPhone, Samsung Galaxy, Xiaomi, phụ kiện và hệ điều hành di động Android, iOS', 2),
(3, 'Máy tính & Phần cứng',    'may-tinh-phan-cung',   'Đánh giá và tin tức về laptop, máy tính để bàn, vi xử lý CPU, card đồ họa GPU, RAM, SSD và thiết bị ngoại vi', 3),
(4, 'Phần mềm & Ứng dụng',    'phan-mem-ung-dung',    'Cập nhật về hệ điều hành Windows, macOS, Linux, ứng dụng văn phòng, công cụ lập trình và mẹo công nghệ hữu ích', 4),
(5, 'Xe & Đời sống số',       'xe-doi-song-so',       'Tin tức về xe điện EV, công nghệ tự hành, năng lượng sạch và chuyển đổi số trong đời sống hàng ngày', 5);


-- ============================================================
--  BANG 3: news - Bai viet tin tuc
-- ============================================================
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(255)     NOT NULL                   COMMENT 'Tieu de bai viet',
    `slug`        VARCHAR(300)     NOT NULL                   COMMENT 'URL than thien SEO, tu dong tao tu tieu de',
    `summary`     TEXT             NOT NULL                   COMMENT 'Tom tat / mo ta ngan hien thi o trang chu va danh sach',
    `content`     LONGTEXT         NOT NULL                   COMMENT 'Noi dung chi tiet bai viet day du',
    `thumbnail`   VARCHAR(255)     NULL     DEFAULT NULL      COMMENT 'Ten file anh luu trong assets/uploads/',
    `category_id` INT UNSIGNED     NOT NULL                   COMMENT 'Khoa ngoai lien ket toi bang categories',
    `views`       INT UNSIGNED     NOT NULL DEFAULT 0         COMMENT 'Luot xem, tu dong +1 khi nguoi doc vao detail.php',
    `status`      TINYINT(1)       NOT NULL DEFAULT 1         COMMENT '1 = Xuat ban hien thi ngoai, 0 = Ban nhap an di',
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    -- Index cot thuong dung trong WHERE / ORDER BY
    KEY `idx_status`     (`status`),
    KEY `idx_category`   (`category_id`),
    KEY `idx_views`      (`views`),
    KEY `idx_created`    (`created_at`),
    -- Index ket hop toi uu nhat cho truy van chinh: WHERE status=1 ORDER BY id DESC
    KEY `idx_status_id`  (`status`, `id`),
    -- Index cho tim kiem full-text theo tieu de va noi dung
    FULLTEXT KEY `ft_title_summary` (`title`, `summary`),

    -- Khoa ngoai: Khi xoa category, bai viet thuoc category do cung bi xoa theo (CASCADE)
    CONSTRAINT `fk_news_category`
        FOREIGN KEY (`category_id`)
        REFERENCES `categories` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Bai viet tin tuc cong nghe chinh cua TechNews';


-- ============================================================
--  DU LIEU MAU: 10 bai viet tin tuc cong nghe thuc te
-- ============================================================
INSERT INTO `news` (`id`, `title`, `slug`, `summary`, `content`, `thumbnail`, `category_id`, `views`, `status`, `created_at`) VALUES

(1,
'Google công bố bước đột phá mới về mô hình AI thế hệ tiếp theo',
'google-cong-bo-buoc-dot-pha-moi-ve-mo-hinh-ai-the-he-tiep-theo',
'Google vừa giới thiệu phiên bản nâng cấp vượt bậc của hệ thống AI tiên tiến, mang lại khả năng suy luận logic vượt trội và tốc độ xử lý nhanh gấp đôi thế hệ trước.',
'<p>Tại sự kiện công nghệ được trông chờ nhất năm, các nhà nghiên cứu hàng đầu của Google đã trình diễn những năng lực ấn tượng của mô hình trí tuệ nhân tạo thế hệ mới. Không chỉ giải quyết thành thạo các bài toán toán học phức tạp bậc đại học, mô hình này còn có khả năng lập trình tự động với độ chính xác đạt mức chuyên gia.</p><p>Điểm đáng chú ý nhất là việc tối ưu hóa mức tiêu thụ năng lượng trong quá trình suy luận, giúp các trung tâm dữ liệu tiết kiệm tới 40% điện năng so với thế hệ trước. Điều này có ý nghĩa lớn trong bối cảnh chi phí vận hành AI đang ngày càng tăng cao.</p><p>Nhiều chuyên gia phân tích nhận định đây là bước tiến vững chắc hướng tới việc triển khai AI trên quy mô toàn cầu, đặc biệt trong các lĩnh vực y tế, khoa học và giáo dục. Dự kiến mô hình sẽ được tích hợp vào các sản phẩm thương mại trong vòng 6 tháng tới.</p>',
'google-ai.jpg', 1, 1420, 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),

(2,
'Thị trường smartphone màn hình gập chứng kiến mức tăng trưởng kỷ lục trong quý III',
'thi-truong-smartphone-man-hinh-gap-tang-truong-ky-luc-quy-3',
'Doanh số điện thoại màn hình gập tăng trưởng kỷ lục nhờ giá thành ngày càng dễ tiếp cận, thiết kế mỏng nhẹ hơn và độ bền bản lề được cải thiện đáng kể.',
'<p>Các nhà sản xuất điện thoại lớn đang liên tục tung ra các mẫu smartphone gập với thiết kế mỏng nhẹ hơn, đồng thời khắc phục hoàn toàn vấn đề nếp gấp trên màn hình vốn là điểm trừ lớn của thế hệ đầu tiên. Người dùng trẻ tuổi tỏ ra đặc biệt hào hứng với dòng gập vỏ sò nhỏ gọn và thời trang.</p><p>Theo báo cáo từ IDC, thị trường điện thoại gập đã tăng trưởng 86% so với cùng kỳ năm ngoái, với tổng doanh số vượt 20 triệu chiếc trong quý. Các thương hiệu dẫn đầu gồm Samsung, Motorola và Huawei đang chiếm hơn 70% thị phần.</p><p>Dự báo trong năm tới, phân khúc điện thoại gập tầm trung với mức giá dưới 15 triệu đồng sẽ chính thức xuất hiện, tạo nên cuộc cạnh tranh khốc liệt giữa các thương hiệu và mở rộng đáng kể tệp người dùng tiếp cận được công nghệ này.</p>',
'foldable-phone.jpg', 2, 980, 1, DATE_SUB(NOW(), INTERVAL 8 DAY)),

(3,
'Đánh giá toàn diện chip xử lý thế hệ mới: GPU tích hợp phá vỡ mọi kỷ lục benchmark',
'danh-gia-toan-dien-chip-xu-ly-the-he-moi-gpu-tich-hop-pha-vo-moi-ky-luc',
'Thế hệ CPU mới cho laptop mang lại hiệu năng đồ họa tích hợp đủ sức chơi mượt mà các tựa game AAA ở độ phân giải 1080p mà không cần card rời.',
'<p>Với tiến trình sản xuất 3nm tiên tiến nhất hiện tại, vi xử lý thế hệ mới không chỉ nâng cao xung nhịp mà còn tích hợp thêm các nhân NPU (Neural Processing Unit) chuyên biệt cho các tác vụ trí tuệ nhân tạo ngay trên thiết bị. Điều này đồng nghĩa với việc các tác vụ như xử lý ảnh, dịch thuật theo thời gian thực và tạo nội dung bằng AI sẽ nhanh hơn gấp nhiều lần.</p><p>Thời lượng pin của các mẫu laptop sử dụng chip này đạt mức kỷ lục trên 18 giờ làm việc văn phòng thực tế, cải thiện khoảng 30% so với thế hệ trước. Đây được coi là lời đáp trả trực tiếp đối với các dòng máy chạy kiến trúc ARM vốn đang được ca ngợi về thời lượng pin.</p><p>Trong bài kiểm tra hiệu năng GPU tích hợp, chip mới đạt điểm số cao hơn 60% so với đối thủ cùng thế hệ. Các tựa game phổ biến như Valorant, CS2 và League of Legends đều chạy ổn định trên 100 FPS ở cài đặt đồ họa cao.</p>',
'cpu-chip.jpg', 3, 765, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),

(4,
'Hệ điều hành mã nguồn mở bùng nổ trong doanh nghiệp: 70% công ty lớn đã chuyển đổi',
'he-dieu-hanh-ma-nguon-mo-bung-no-trong-doanh-nghiep-70-phan-tram-da-chuyen-doi',
'Tính bảo mật vượt trội, khả năng tùy biến cao và tiết kiệm hàng triệu đồng chi phí bản quyền là những lý do hàng đầu khiến doanh nghiệp đẩy mạnh chuyển đổi sang Linux.',
'<p>Khảo sát của Linux Foundation thực hiện trên hơn 10.000 doanh nghiệp toàn cầu cho thấy hơn 70% tổ chức đã tích hợp giải pháp mã nguồn mở vào hạ tầng máy chủ của mình trong 3 năm qua. Tại Việt Nam, xu hướng này cũng đang diễn ra mạnh mẽ, đặc biệt trong lĩnh vực tài chính ngân hàng và cơ quan nhà nước.</p><p>Nhờ sự hỗ trợ mạnh mẽ từ cộng đồng và các tập đoàn lớn như Red Hat (IBM), Canonical và SUSE, tính tương thích của các ứng dụng văn phòng và phần mềm chuyên ngành trên hệ điều hành Linux đã đạt mức gần như hoàn hảo. LibreOffice 7.6 đã có thể mở và chỉnh sửa hầu hết các file Microsoft Office mà không bị lỗi định dạng.</p><p>Các chuyên gia bảo mật cũng chỉ ra rằng hệ thống chạy Linux ít bị tấn công bởi ransomware và malware hơn tới 85% so với Windows. Đây là yếu tố quyết định trong việc các ngân hàng và tổ chức tài chính quyết định chuyển đổi sang nền tảng mã nguồn mở.</p>',
'open-source.jpg', 4, 530, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),

(5,
'Pin thể rắn cho xe điện chính thức bước vào giai đoạn thương mại hóa đại trà',
'pin-the-ran-cho-xe-dien-chinh-thuc-buoc-vao-giai-doan-thuong-mai-hoa-dai-tra',
'Pin thể rắn thế hệ mới hứa hẹn đưa tầm vận hành của xe điện lên trên 1.200 km một lần sạc và rút ngắn thời gian sạc đầy xuống chỉ còn 10 phút.',
'<p>Toyota, Samsung SDI và QuantumScape đồng loạt công bố các nguyên mẫu xe thử nghiệm thực tế sử dụng công nghệ pin thể rắn với thành tích ấn tượng: vận hành liên tục hơn 1.200 km trên một lần sạc trong điều kiện thời tiết thực tế, bao gồm cả nhiệt độ âm 20 độ C.</p><p>Điểm đột phá quan trọng nhất là độ an toàn chống cháy nổ gần như tuyệt đối so với pin lithium-ion truyền thống vốn tiềm ẩn nguy cơ bắt lửa khi va chạm mạnh hoặc sạc không đúng cách. Ngoài ra, mật độ năng lượng cao hơn gấp đôi giúp giảm đáng kể trọng lượng khối pin, cải thiện khả năng tăng tốc và xử lý đường cong của xe.</p><p>Lộ trình sản xuất thương mại dự kiến sẽ bắt đầu từ năm 2027, với giá thành được dự báo tiệm cận pin lithium-ion vào khoảng 2029. Đây sẽ là bước ngoặt quyết định giúp xe điện thực sự cạnh tranh ngang bằng với xe xăng về cả hiệu suất lẫn sự tiện lợi.</p>',
'electric-car.jpg', 5, 1210, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),

(6,
'Bí quyết tăng FPS vượt trội cho máy tính chơi game mà không cần nâng cấp phần cứng',
'bi-quyet-tang-fps-vuot-troi-cho-may-tinh-choi-game-ma-khong-can-nang-cap-phan-cung',
'Bật Resizable BAR, tối ưu driver GPU, dọn dẹp tiến trình nền và chỉnh đúng cài đặt Windows có thể giúp bạn tăng chỉ số FPS lên tới 30% hoàn toàn miễn phí.',
'<p>Nhiều game thủ thường bỏ qua các bước thiết lập hệ thống cơ bản nhưng lại mang lại hiệu quả rất rõ rệt mà không tốn một đồng chi phí. Bài viết này tổng hợp đầy đủ các phương pháp được kiểm chứng thực tế từ cộng đồng gaming quốc tế.</p><p><strong>1. Kích hoạt Resizable BAR (Smart Access Memory):</strong> Vào BIOS, bật Resizable BAR hoặc SAM. Với các card đồ họa hỗ trợ (RTX 3000 series trở lên, RX 6000 trở lên), tính năng này có thể tăng FPS từ 5 đến 15% trong hầu hết các tựa game.</p><p><strong>2. Cập nhật và cài đặt driver GPU sạch:</strong> Dùng DDU (Display Driver Uninstaller) để gỡ hoàn toàn driver cũ trước khi cài phiên bản mới nhất. Driver cũ hoặc cài chồng có thể gây stuttering và giảm hiệu năng.</p><p><strong>3. Chỉnh High Performance Plan trong Windows:</strong> Control Panel > Power Options > High Performance. Chế độ Balanced mặc định của Windows có thể giảm xung nhịp CPU không cần thiết trong lúc chơi game.</p><p><strong>4. Tắt Xbox Game Bar và các overlay không cần thiết:</strong> Những công cụ chạy nền như Discord overlay, GeForce Experience overlay đều tiêu tốn tài nguyên và có thể gây giảm FPS từ 2-5%.</p>',
'pc-gaming.jpg', 3, 640, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),

(7,
'Công cụ lập trình AI mới giúp lập trình viên viết code nhanh hơn 50% và ít lỗi hơn',
'cong-cu-lap-trinh-ai-moi-giup-lap-trinh-vien-viet-code-nhanh-hon-50-phan-tram',
'Trình soạn thảo code thế hệ mới tích hợp trợ lý AI học theo phong cách của từng lập trình viên, tự động phát hiện lỗ hổng bảo mật và đề xuất tối ưu hóa hiệu năng ngay lúc gõ phím.',
'<p>Công cụ lập trình mới được phát triển nhằm giảm thiểu tối đa các công việc lặp đi lặp lại và xử lý thủ công vốn chiếm hơn 40% thời gian của một lập trình viên trung bình trong ngày làm việc. Điểm khác biệt lớn nhất so với các công cụ gợi ý code hiện tại là khả năng học và thích nghi với phong cách lập trình, quy ước đặt tên biến và kiến trúc dự án của từng cá nhân theo thời gian.</p><p>Ngoài khả năng tự động sinh test case với độ bao phủ cao, công cụ còn cung cấp giải thích chi tiết nguyên nhân lỗi runtime ngay trong IDE, kèm theo 3 đến 5 phương án sửa lỗi với ưu nhược điểm rõ ràng để lập trình viên lựa chọn. Tính năng phát hiện lỗ hổng bảo mật theo thời gian thực (SQL injection, XSS, path traversal...) được tích hợp trực tiếp mà không cần chạy công cụ scan riêng biệt.</p><p>Trong thử nghiệm thực tế với 500 lập trình viên từ các công ty khác nhau, năng suất trung bình tăng 52% trong tháng đầu tiên sử dụng và 73% sau 3 tháng khi công cụ đã quen với phong cách làm việc của người dùng.</p>',
'code-editor.jpg', 4, 880, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),

(8,
'iPhone thế hệ tiếp theo sẽ trang bị camera tiềm vọng kính khẩu độ lớn chưa từng có',
'iphone-the-he-tiep-theo-trang-bi-camera-tiem-vong-kinh-khau-do-lon-chua-tung-co',
'Các nguồn tin nội bộ tiết lộ Apple đang thử nghiệm module camera hoàn toàn mới với khẩu độ f/1.4 và cảm biến 1 inch, hứa hẹn cách mạng hóa chụp ảnh đêm trên smartphone.',
'<p>Theo thông tin từ các nhà cung cấp linh kiện của Apple tại châu Á, thế hệ iPhone tiếp theo đang được thử nghiệm với module camera tiềm vọng kính (periscope) thế hệ thứ hai, được cải tiến căn bản so với phiên bản hiện tại. Điểm nhấn lớn nhất là cảm biến hình ảnh 1 inch lớn hơn hẳn thế hệ hiện tại với khẩu độ tối đa f/1.4, đồng nghĩa với việc hấp thụ ánh sáng nhiều hơn gấp đôi.</p><p>Hệ thống chống rung quang học OIS cũng được nâng cấp lên thế hệ mới với khả năng bù chấn động lên tới 5 trục, giúp quay video 4K ở độ ổn định vượt trội ngay cả khi di chuyển. Khả năng zoom quang học dự kiến sẽ đạt 10x, ngang tầm với các máy ảnh compact chuyên dụng.</p><p>Thị trường cũng đồng thời đón nhận tin đồn về việc Apple có thể áp dụng công nghệ làm mát buồng hơi (vapor chamber) tương tự Android cao cấp, giải quyết dứt điểm vấn đề nhiệt độ cao khi sử dụng camera trong thời gian dài mà các iPhone thế hệ trước vẫn gặp phải.</p>',
NULL, 2, 445, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),

(9,
'Việt Nam tăng tốc triển khai 5G: Vùng phủ sóng mở rộng ra 20 tỉnh thành trong năm nay',
'viet-nam-tang-toc-trien-khai-5g-vung-phu-song-mo-rong-20-tinh-thanh',
'Các nhà mạng lớn đẩy nhanh lộ trình phủ sóng 5G tại Việt Nam, mục tiêu đưa tốc độ trung bình đạt 500 Mbps đến tay người dùng cuối trước cuối năm.',
'<p>Viettel, Vinaphone và Mobifone đã chính thức công bố lộ trình triển khai 5G mở rộng ra 20 tỉnh thành trong năm nay, tập trung vào các khu công nghiệp, khu chế xuất và các thành phố đông dân. Hiện tại, 5G đã hoạt động ổn định tại Hà Nội, TP. Hồ Chí Minh, Đà Nẵng và một số khu vực thử nghiệm.</p><p>Điểm đáng chú ý trong đợt triển khai lần này là việc áp dụng công nghệ 5G SA (Standalone - Độc lập) thay vì 5G NSA như trước đây. Với kiến trúc 5G SA, độ trễ mạng có thể giảm xuống dưới 1ms, mở ra khả năng ứng dụng trong các lĩnh vực đòi hỏi phản hồi theo thời gian thực như điều khiển robot từ xa, phẫu thuật từ xa và xe tự hành.</p><p>Bộ Thông tin và Truyền thông cũng đặt mục tiêu đến năm 2026, Việt Nam phải có ít nhất 3 doanh nghiệp sản xuất thiết bị 5G có thể xuất khẩu ra thị trường quốc tế, đặc biệt trong lĩnh vực anten và module thu phát tín hiệu.</p>',
NULL, 2, 320, 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),

(10,
'Tổng quan công nghệ xe điện 2026: Cuộc đua giá thành và tầm vận hành bước vào giai đoạn quyết định',
'tong-quan-cong-nghe-xe-dien-2026-cuoc-dua-gia-thanh-va-tam-van-hanh',
'Giá xe điện trung bình toàn cầu dự kiến giảm xuống ngang bằng xe xăng vào cuối năm 2026, đánh dấu thời điểm xe điện thực sự trở thành lựa chọn chính cho đại đa số người tiêu dùng.',
'<p>Báo cáo mới nhất từ BloombergNEF cho thấy chi phí sản xuất pin lithium-ion đã giảm xuống mức thấp lịch sử, dưới 100 USD/kWh. Với ngưỡng này, giá bán lẻ của xe điện tầm trung sẽ tương đương hoặc thậm chí thấp hơn xe xăng cùng phân khúc khi tính cả chi phí nhiên liệu và bảo dưỡng trong 5 năm sử dụng.</p><p>Tại thị trường Việt Nam, VinFast đang đẩy mạnh chiến lược ra mắt dòng xe điện phổ thông với giá khởi điểm dưới 400 triệu đồng, trong khi các thương hiệu Trung Quốc như BYD, Chery và Wuling cũng chuẩn bị gia nhập thị trường với các dòng xe mini EV giá rẻ.</p><p>Hạ tầng sạc nhanh cũng đang được đầu tư mạnh với hơn 2.000 trụ sạc DC công suất cao (150kW+) dự kiến được lắp đặt trên toàn quốc trong năm 2026, giải quyết dần dần nỗi lo sạc xe của người dùng tại các tuyến đường dài.</p>',
'electric-car.jpg', 5, 755, 0, NOW());


-- Bat lai kiem tra khoa ngoai sau khi import xong
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  CAC CHI MUC BO SUNG (neu can chay them sau khi da co du lieu)
-- ============================================================
-- Neu muon kiem tra query dang chay cham, chay lenh sau:
-- EXPLAIN SELECT n.*, c.name FROM news n JOIN categories c ON n.category_id = c.id WHERE n.status = 1 ORDER BY n.id DESC LIMIT 0, 6;
