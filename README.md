# Fashion Store – Nhóm 08

**Trường Đại học Sư phạm – Đại học Đà Nẵng**

Fashion Store là dự án website bán hàng thời trang trực tuyến, được xây dựng trong khuôn khổ học phần **Lập trình Web**. Dự án sử dụng PHP và MySQL để xây dựng các chức năng quản lý sản phẩm, tài khoản người dùng, giỏ hàng, đơn hàng và quản trị hệ thống.

## 1. Công nghệ sử dụng

- **PHP >= 8.1:** Xử lý nghiệp vụ phía máy chủ.
- **MySQL / phpMyAdmin:** Quản lý cơ sở dữ liệu `ltweb_nhom08`.
- **PDO:** Kết nối và thao tác với cơ sở dữ liệu.
- **HTML5, CSS3:** Xây dựng cấu trúc và giao diện website.
- **JavaScript:** Xử lý tương tác, AJAX và Fetch API.
- **Bootstrap:** Hỗ trợ thiết kế giao diện responsive.
- **Session và Cookie:** Hỗ trợ quản lý đăng nhập và giỏ hàng.

## 2. Cấu trúc thư mục dự án

Dự án được tổ chức thành các thư mục và tệp chính như sau:

| Thư mục / Tệp | Chức năng |
|---|---|
| `index.php` | Trang chủ, giới thiệu sản phẩm và danh mục |
| `danh-sach.php` | Hiển thị, tìm kiếm và lọc sản phẩm |
| `chi-tiet.php` | Hiển thị thông tin chi tiết sản phẩm |
| `gio-hang.php` | Quản lý giỏ hàng |
| `lien-he.php` | Biểu mẫu liên hệ |
| `dang-ky.php` | Đăng ký tài khoản |
| `dang-nhap.php` | Đăng nhập |
| `dang-xuat.php` | Đăng xuất |
| `don-hang.php` | Quản lý và xem thông tin đơn hàng |
| `quan-tri.php` | Trang quản trị hệ thống |
| `sua-san-pham.php` | Xử lý chỉnh sửa sản phẩm |
| `inc/` | Chứa các tệp cấu hình, kết nối CSDL và hàm dùng chung |
| `src/` | Chứa các lớp xử lý dữ liệu và nghiệp vụ |
| `database/` | Chứa cấu trúc, dữ liệu mẫu và sơ đồ cơ sở dữ liệu |
| `api/` | Chứa các tệp xử lý API |
| `js/` | Chứa mã JavaScript |
| `css/` | Chứa mã CSS |
| `images/` | Chứa hình ảnh sản phẩm và tài nguyên giao diện |
| `kiemtra/` | Lưu hình ảnh minh chứng kiểm thử |
| `logs/` | Lưu nhật ký lỗi hệ thống |

*Lưu ý: Danh sách trên là các thành phần chính được mô tả cho dự án; cần đối chiếu với cấu trúc mã nguồn thực tế trước khi hoàn thiện README.*

## 3. Hướng dẫn cài đặt và chạy dự án Bài 7

### 3.1. Yêu cầu môi trường
- PHP 8.1 trở lên, có PDO MySQL.
- XAMPP (Apache, MySQL) và Composer.
- Trình duyệt web.

### 3.2. Cài đặt thư viện
Mở Terminal tại thư mục gốc dự án và chạy:

```powershell
composer install
```

### 3.3. Cấu hình môi trường
Sao chép `.env.example` thành `.env`:

```powershell
Copy-Item .env.example .env
```

Cấu hình mẫu để giảng viên chạy trên XAMPP:

```ini
APP_DEBUG=true
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ltweb_nhom08
DB_USERNAME=root
DB_PASSWORD=
```

Chỉnh các giá trị trong `.env` nếu MySQL trên máy dùng tài khoản, mật khẩu hoặc cổng khác. Không commit `.env` lên GitHub.

### 3.4. Khởi tạo cơ sở dữ liệu
Bật MySQL trong XAMPP, truy cập `http://localhost/phpmyadmin/` và lần lượt nhập:

1. `database/schema.sql`
2. `database/seed.sql`

**Chú ý:** `schema.sql` có lệnh `DROP DATABASE`, chỉ nhập trên cơ sở dữ liệu thử nghiệm mới hoặc sau khi đã sao lưu. Không nhập lại trên CSDL đang có dữ liệu cần giữ.

### 3.5. Tài khoản CSDL quyền tối thiểu
Dùng tài khoản MySQL có quyền quản trị để thực hiện các câu lệnh trong `database/tai-khoan.sql`. Thay chuỗi mật khẩu giữ chỗ bằng một mật khẩu thử nghiệm riêng trước khi chạy, không ghi mật khẩu thật vào Git.

Sau đó chỉnh `.env` để sử dụng `DB_USERNAME=nhom08_app`, `DB_PASSWORD` bằng mật khẩu vừa đặt, và `DB_HOST` khớp với host của tài khoản được tạo trong SQL.

Tài khoản này chỉ được cấp SELECT, INSERT, UPDATE, DELETE trên `ltweb_nhom08.*`; không có CREATE, DROP, ALTER.

### 3.6. Chạy bằng PHP Development Server
Mở Terminal tại thư mục gốc dự án:

```powershell
php -S localhost:8000
```

Truy cập `http://localhost:8000/`.

### 3.7. Chạy bằng Apache trong XAMPP
Đặt thư mục dự án trong `C:\xampp\htdocs\ltweb-doan-nhom08`, bật Apache và MySQL rồi truy cập:

`http://localhost/ltweb-doan-nhom08/`

Tệp `.htaccess` chỉ được Apache áp dụng, không có tác dụng với `php -S`.

### 3.8. Kiểm thử sau cài đặt
- Truy cập trang chủ, đăng nhập, tìm kiếm, giỏ hàng, đơn hàng và quản trị.
- Chạy các request trong `api.http` với cookie và CSRF hợp lệ.
- Thực hiện các ca trong `tests/bao-mat.http` theo hướng dẫn.
- Chạy `composer audit --locked`.
- Kiểm tra `logs/php-error.log`, `logs/bao-mat.log` và Console trình duyệt.

Không lưu mật khẩu, cookie phiên hoặc token CSRF thật trong mã nguồn và báo cáo.

## 4. Tài khoản kiểm thử

Các tài khoản dưới đây được sử dụng để kiểm tra chức năng đăng nhập và phân quyền (nếu đã được tạo trong cơ sở dữ liệu):

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Quản trị viên | `admin@fashionstore.com` | `123456` |
| Khách hàng | `khach1@gmail.com` | `555555` |

*Không sử dụng các mật khẩu kiểm thử này cho tài khoản thực tế hoặc môi trường triển khai công khai.*

## 5. Các chức năng chính

Dự án hướng đến việc xây dựng một website bán hàng thời trang với các chức năng:

- Hiển thị danh sách sản phẩm và danh mục sản phẩm.
- Tìm kiếm, lọc, sắp xếp và phân trang sản phẩm.
- Xem thông tin chi tiết sản phẩm.
- Quản lý giỏ hàng.
- Đăng ký, đăng nhập và đăng xuất tài khoản.
- Quản lý thông tin đơn hàng.
- Quản lý sản phẩm và dữ liệu thông qua trang quản trị.
- Tiếp nhận thông tin liên hệ từ khách hàng.
- Sử dụng cơ sở dữ liệu quan hệ để lưu trữ và quản lý dữ liệu.
- Xử lý dữ liệu phía máy chủ bằng PHP và PDO.

## 6. Thành viên nhóm

- **Lớp:** 24CNTT2
- **Khoa:** Toán – Tin
- **Trường:** Đại học Sư phạm – Đại học Đà Nẵng

**Danh sách thành viên:**

1. **Nguyễn Sơn Chu** – MSSV: 3120224020
2. **Ngô Hương Giang** – MSSV: 3120224040
3. **Lê Văn Mạnh** – MSSV: 3120224094
4. **KEOPHOXAY Phaeva** – MSSV: 3120224182
5. **XAYYATIEW Phoutsana** – MSSV: 3120224184
## 7. Mục tiêu dự án

Thông qua dự án Fashion Store, nhóm vận dụng kiến thức đã học trong học phần Lập trình Web để xây dựng một website bán hàng trực tuyến. Đồng thời, nhóm có cơ hội thực hành thiết kế cơ sở dữ liệu, lập trình PHP hướng đối tượng, xử lý dữ liệu với MySQL, xây dựng giao diện người dùng và kiểm thử các chức năng của hệ thống.

---

**Fashion Store – Nhóm 08**  
*Dự án học tập – Học phần Lập trình Web*