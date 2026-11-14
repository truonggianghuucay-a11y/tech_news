# TechNews - Website Tin Tức Công Nghệ

Website tin tức công nghệ được xây dựng bằng **PHP thuần**, **MySQL** và **Bootstrap 5 (CSS Only)**.

---

## 🚀 Đặc điểm kỹ thuật

- **Backend:** PHP thuần (không sử dụng framework).
- **Cơ sở dữ liệu:** MySQL / MariaDB (Database: `tech_news`, PDO prepared statements chống SQL Injection).
- **Frontend:** HTML5, CSS3, Bootstrap 5 CSS & Bootstrap Icons CSS.
- **Tiêu chuẩn:** **100% không JavaScript** (không file `.js`, không thẻ `<script>`, không jQuery, không AJAX).
- **Bảo mật:**
  - Mật khẩu băm an toàn bằng `password_hash()` và `password_verify()`.
  - Phòng chống XSS với `htmlspecialchars()`.
  - Xác thực phiên làm việc qua Session ở toàn bộ trang Admin.
  - Kiểm tra định dạng (whitelist JPG/PNG/WEBP) và dung lượng file ảnh upload.
  - Thao tác xóa sử dụng phương thức POST kèm trang HTML/PHP xác nhận riêng biệt.
- **Thư viện lõi:** Tái sử dụng trọn vẹn `lib.php` (`Database`, `Session`, `Helper`, `paging()`).

---

## 📂 Cấu trúc thư mục

```text
technewsweb/
│
├── lib.php                       # Thư viện dùng chung (Database, Session, paging, Helper)
├── config.php                    # Cấu hình session, timezone, clean XSS, make_slug
├── database.sql                  # CSDL tech_news và 10 bài viết mẫu
│
├── assets/
│   ├── css/
│   │   ├── style.css             # CSS giao diện người dùng
│   │   └── admin.css             # CSS trang quản trị
│   └── uploads/                  # Thư mục chứa hình ảnh bài viết
│
├── includes/                     # Layout người dùng
│   ├── header.php                # Head, Bootstrap 5 CSS
│   ├── navbar.php                # Menu điều hướng & tìm kiếm GET
│   ├── sidebar.php               # Đọc nhiều nhất & danh mục
│   └── footer.php                # Chân trang (không script)
│
├── index.php                     # Trang chủ (Hero feature + phân trang)
├── category.php                  # Xem tin theo chuyên mục (kèm phân trang)
├── detail.php                    # Chi tiết bài viết & bài liên quan
├── search.php                    # Tìm kiếm tin tức qua GET (kèm phân trang)
│
└── admin/                        # Trang Quản trị viên
    ├── index.php                 # Dashboard thống kê
    ├── login.php                 # Đăng nhập Admin
    ├── logout.php                # Đăng xuất
    ├── includes/                 # Layout quản trị
    ├── categories/               # Quản lý chuyên mục (CRUD)
    └── news/                     # Quản lý bài viết (CRUD + Upload ảnh)
```

---

## 🛠️ Hướng dẫn cài đặt & chạy thử

1. **Khởi động MySQL và Apache:** Khởi động Apache và MySQL trong XAMPP hoặc Laragon.
2. **Import CSDL:**
   - Mở phpMyAdmin: `http://localhost/phpmyadmin/`
   - Chọn tab **Import** và chọn file `database.sql` trong thư mục dự án (hoặc chạy lệnh: `mysql -u root < database.sql`).
3. **Mở website:**
   - **Giao diện người dùng:** `http://localhost/technewsweb/`
   - **Khu vực Quản trị:** `http://localhost/technewsweb/admin/login.php`
     - **Tài khoản:** `admin`
     - **Mật khẩu:** `admin123`
