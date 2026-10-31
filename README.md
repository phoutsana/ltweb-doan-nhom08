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

## 3. Hướng dẫn cài đặt và chạy dự án

**Yêu cầu môi trường:**
- XAMPP có hỗ trợ PHP từ phiên bản 8.1 trở lên.
- Apache và MySQL.
- Trình duyệt web.
- Visual Studio Code (khuyến nghị).

**Bước 1: Tải mã nguồn**

Clone dự án về máy tính:

```bash
git clone https://github.com/phoutsana/ltweb-doan-nhom08.git
cd ltweb-doan-nhom08
composer install
```

**Bước 2: Khởi động XAMPP**

Mở XAMPP Control Panel và khởi động Apache, MySQL.

**Bước 3: Tạo cơ sở dữ liệu**

1. Truy cập `http://localhost/phpmyadmin/`.
2. Tạo cơ sở dữ liệu có tên `ltweb_nhom08`.
3. Import tệp `database/schema.sql`.
4. Import tệp `database/seed.sql` để thêm dữ liệu mẫu (nếu có).

**Bước 4: Kiểm tra cấu hình kết nối**

Mở `inc/db.php` và kiểm tra thông tin kết nối cơ sở dữ liệu cho phù hợp với môi trường máy tính.

**Bước 5: Truy cập website**

Nếu sử dụng XAMPP, truy cập đường dẫn tương ứng với thư mục dự án trong `htdocs`, ví dụ:

```text
http://localhost/nhom08/ltweb-doan-nhom08/
```

Nếu chạy bằng PHP Development Server, mở Terminal tại thư mục dự án và thực hiện:

```bash
php -S localhost:8000
```

Sau đó truy cập:

```text
http://localhost:8000/
```

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