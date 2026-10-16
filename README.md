
# Fashion Store – Nhóm 08

Website cửa hàng thời trang được xây dựng bằng PHP, HTML, CSS,
JavaScript và dữ liệu JSON.

## 1. Công nghệ

- PHP >= 8.1
- Composer và PSR-4 autoload
- HTML5, CSS3, JavaScript
- Bootstrap (nếu được sử dụng trong giao diện)
- Session và cookie
- JSON để lưu dữ liệu sản phẩm

## 2. Cấu trúc chính

- `index.php`: Trang chủ
- `danh-sach.php`: Danh sách sản phẩm
- `chi-tiet.php`: Chi tiết sản phẩm
- `gio-hang.php`: Giỏ hàng
- `lien-he.php`: Biểu mẫu liên hệ
- `dang-nhap.php`: Đăng nhập
- `dang-xuat.php`: Đăng xuất
- `quan-tri.php`: Quản trị liên hệ
- `inc/`: Cấu hình, giao diện dùng chung và các hàm hỗ trợ
- `src/`: Các lớp PHP
- `data/san-pham.json`: Dữ liệu sản phẩm
- `storage/`: Dữ liệu phát sinh khi chạy
- `logs/`: Nhật ký lỗi
- `uploads/`: Ảnh tải lên
- `kiemtra/`: Ảnh chụp kiểm thử

## 3. Cài đặt và chạy

Yêu cầu PHP >= 8.1 và Composer.

Tại thư mục gốc của dự án, chạy:

```bash
composer install
php -S localhost:8000
```

Mở trình duyệt tại:

http://localhost:8000/

Có thể sử dụng XAMPP bằng cách đặt dự án trong thư mục `htdocs`
và truy cập đường dẫn tương ứng trên localhost.

## 4. Tài khoản kiểm thử

Tài khoản quản trị mẫu:
- Email: admin@fashionstore.com
- Mật khẩu: Admin@123

Chỉ sử dụng tài khoản mẫu trong môi trường thực hành.

## 5. Chức năng

- Trang chủ và danh sách sản phẩm
- Tìm kiếm, lọc và sắp xếp sản phẩm (sau khi hoàn thiện)
- Xem chi tiết và sản phẩm đã xem gần đây (sau khi hoàn thiện)
- Giỏ hàng sử dụng session
- Biểu mẫu liên hệ và tải ảnh (sau khi hoàn thiện)
- Đăng nhập, đăng xuất và quản trị liên hệ (sau khi hoàn thiện)

## 6. Thành viên

- Nhóm 08 – Đại học Sư phạm, Đại học Đà Nẵng
- Bổ sung tên và nhiệm vụ của từng thành viên tại đây.

## 7. Lưu ý

Các chức năng ghi chú "sau khi hoàn thiện" chỉ được xem là hoàn thành
sau khi đã kiểm thử thực tế. Không đưa dữ liệu nhạy cảm hoặc mật khẩu
thật vào kho mã nguồn.