-- seed.sql: Chèn dữ liệu mẫu cho Fashion Store (Khớp 100% với dữ liệu hiện tại trên phpMyAdmin)

-- 1. Chèn tài khoản mẫu
-- 1. Chèn tài khoản mẫu
-- Admin: admin@fashionstore.com / 123456
-- Khách hàng 1: khach1@gmail.com / 555555
-- Khách hàng 2: khach2@gmail.com / 123456

INSERT INTO nguoi_dung (email, mat_khau, ho_ten, vai_tro, ngay_tao) VALUES
('admin@fashionstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên', 'quan_tri', NOW()),
('khach1@gmail.com', '$2y$10$jMd3STvdUHTq/dcPXWLI2exernFtpfkb11hYIVKT7VBv2iuUNZ68K', 'Nguyễn Văn Khách', 'khach_hang', NOW()),
('khach2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Mua', 'khach_hang', NOW());
-- 2. Chèn danh mục
INSERT INTO danh_muc (ten, slug) VALUES
('Áo Nam', 'ao-nam'),
('Quần Nam', 'quan-nam'),
('Thời Trang Nữ', 'thoi-trang-nu'),
('Phụ Kiện', 'phu-kien'),
('Bộ Sưu Tập Cũ', 'bo-suu-tap-cu');

-- 3. Chèn 30 sản phẩm (Chính xác theo CSDL hiện tại)
INSERT INTO san_pham (danh_muc_id, ten, gia, ton_kho, anh, mau_sac, kich_thuoc, video, mo_ta, ngay_tao) VALUES
-- Sản phẩm 1-12
(1, 'Áo thun nam basic', 150000, 50, 'images/ao-thun-nam.jpg', 'Xanh', 'M, L, XL', 'https://www.youtube.com/embed/Q4T5uVfQ03w?si=dF3zYc-sNa3FOJVl', 'Áo polo nam thiết kế đơn giản, dễ phối đồ.', NOW()),
(1, 'Áo sơ mi nữ thanh lịch', 350000, 20, 'images/ao-so-mi-nu.jpg', 'Trắng', 'S, M, L', 'https://www.youtube.com/embed/Hf_dMGhF7zM?si=63-j2AgRkYY17aDy', 'Áo sơ mi nữ màu trắng, phù hợp đi học và đi làm.', NOW()),
(1, 'Áo khoác gió', 450000, 0, 'images/Ao_khoac_gio.jpg', 'Đen', '29, 30, 31, 32', 'https://www.youtube.com/embed/C8373DrkI0w?si=BDVWzNU5Fe57z0c3', 'Áo khoác gió nhẹ, phù hợp sử dụng khi đi ngoài trời...', NOW()),
(1, 'Áo polo nam', 220000, 35, 'images/Ao_polo_nam.jpg', 'Trắng', 'M, L, XL', 'https://www.youtube.com/embed/1at4WHmWhzA?si=oY-qAgdjuLnLYGux', 'Áo polo năng động, trẻ trung.', NOW()),
(1, 'Áo hoodie nam', 400000, 15, 'images/ao-hoodie-nam.jpg', 'Đen', '29, 30, 31, 32', 'https://www.youtube.com/embed/U08_Wu7wPn8?si=imH9DASAIDKMcKSP', 'Áo hoodie nam phong cách trẻ trung.', NOW()),
(1, 'Áo khoác bomber', 500000, 10, 'images/ao-bomber-nam.jpg', 'Đen', 'M, L, XL', 'https://www.youtube.com/embed/lwOYguJR95Y?si=acGZD6ajZKHAKmp4', 'Áo khoác bomber phong cách trẻ trung.', NOW()),
(1, 'Áo thun nam xanh', 180000, 25, 'images/ao-thun-xanh.jpg', 'Xanh', 'M, L, XL', 'https://www.youtube.com/embed/Mpii6eYHSqk?si=Mkt3-09k-iAwNHdU', 'Áo thun nam màu xanh đơn giản và dễ mặc.', NOW()),
(1, 'Chân váy nữ chữ A', 320000, 30, 'images/chan-vay-nu.jpg', 'Đen', 'M, L, XL', 'https://www.youtube.com/embed/7IO8IB0DCvY?si=0tS1NXcnzt20UhJ_', 'Chân váy form chữ A tôn dáng, năng động và trẻ tru...', NOW()),
(2, 'Quần jean nam dáng suông', 650000, 12, 'images/quan-jean-nam.jpg', 'Đen', '29, 30, 31, 32', 'https://www.youtube.com/embed/yUdXs2msI_s?si=fwErv-M-9olib0PX', 'Quần jean form suông, chất bò cao cấp không phai m...', NOW()),
(2, 'Quần kaki nam', 400000, 25, 'images/Quan_kaki_nam.jpg', 'Vàng Đất', '29, 30, 31, 32', 'https://www.youtube.com/embed/CRd_dkot4BM?si=XNjcglVwoKsfcnN4', 'Quần kaki nam phù hợp đi học và đi làm.', NOW()),
(2, 'Quần short nam', 250000, 40, 'images/Quan_short_nam.jpg', 'Đen, Trắng', 'M, L, XL', 'https://www.youtube.com/embed/ABYuNdnU7Jk?si=v4j2jiIHc-M6HOrG', 'Quần short nam thoải mái cho hoạt động hằng ngày.', NOW()),
(2, 'Quần jogger nam', 300000, 0, 'images/quan-jogger-nam.jpg', 'Đen, Trắng', 'M, L, XL', 'https://www.youtube.com/embed/g1aicLHOiks?si=qB_2YpZ0JTyFlWNv', 'Quần jogger nam thoải mái cho phong cách năng động...', NOW()),

-- Sản phẩm 13-30
(2, 'Quần đùi mặc nhà', 100000, 60, 'images/quan-dui.jpg', 'Đen', 'M, L, XL', 'https://www.youtube.com/embed/kVR902BXfyQ?si=OYBCXOmcXy0TBkzY', 'Mát mẻ, thoải mái.', NOW()),
(2, 'Quần kaki ống rộng', 380000, 18, 'images/quan-kaki.jpg', 'Kem', 'M, L, XL', 'https://www.youtube.com/embed/PX-KZoPpIRA?si=S7m6pPsAcwibt7bj', 'Phong cách trẻ trung hiện đại.', NOW()),
(2, 'Quần jean rách gối cá tính', 450000, 8, 'images/quan-rach.jpg', 'Xanh', 'M, L, XL', 'https://www.youtube.com/embed/ZLDnFExYuMA?si=Uexf_TA2AJC_daNh', 'Dành cho các bạn thích sự phá cách.', NOW()),
(3, 'Đầm nữ dự tiệc sang trọng', 700000, 10, 'images/dam-nu-1.jpg', 'Đỏ', 'S, M, L', 'https://www.youtube.com/embed/fIXMbE1Wehs?si=8_vPWWiLZtSY8ckP', 'Đầm thiết kế thanh lịch, sang trọng.', NOW()),
(3, 'Áo kiểu nữ trễ vai', 250000, 30, 'images/ao-nu-1.jpg', 'Trắng', 'S, M, L', 'https://www.youtube.com/embed/OXVcvDGrK34?si=xMKLGZw5kv4foYP0', 'Áo kiểu nữ tính, điệu đà.', NOW()),
(3, 'Chân váy chữ A ngắn đẹp', 220000, 25, 'images/chan-vay-1.jpg', 'Trắng', 'S, M, L', 'https://www.youtube.com/embed/SWIFHkGyAHU?si=U0RnGQeQs5aR40Gz', 'Chân váy dễ phối đồ.', NOW()),
(3, 'Áo khoác dạ nữ', 850000, 5, 'images/ao-da-nu.jpg', 'Xám', 'M, L, XL', 'https://www.youtube.com/embed/wlz3Zf-E0TA?si=FfUtCzxT2xMDQTUG', 'Áo khoác dạ cao cấp mùa đông.', NOW()),
(3, 'Quần ống rộng nữ', 320000, 22, 'images/quan-ong-rong.jpg', 'Xanh', 'S, M, L', 'https://www.youtube.com/embed/jmrUHtYYYKk?si=7UiXMqC8aOKNTGdd', 'Thoải mái, thời trang.', NOW()),
(3, 'Đầm maxi dạo biển', 450000, 14, 'images/dam-maxi.jpg', 'Trắng', 'S, M, L', 'https://www.youtube.com/embed/dnHd3kgEsnk?si=0uHpIBtqjcEd8zka', 'Thướt tha đi biển.', NOW()),
(3, 'Áo thun nữ croptop', 130000, 50, 'images/croptop.jpg', 'Đen', 'S, M, L', 'https://www.youtube.com/embed/8Sb_2jlzdw0?si=MvubiIRwhMBzj31T', 'Trẻ trung năng động.', NOW()),
(3, 'Set bộ đồ mặc nhà nữ', 350000, 20, 'images/set-do.jpg', 'Trắng,Đen', 'S, M, L', 'https://www.youtube.com/embed/ZaY65ftJSsg?si=YpsuXh2RzKimDkYT', 'Tiện lợi, thoải mái.', NOW()),
(4, 'Thắt lưng da nam cao cấp', 200000, 45, 'images/phu-kien-1.jpg', 'Đen', 'FreeSize', 'https://www.youtube.com/embed/GCnbXM-Us9E?si=IgAvIfIWheWaqcpW', 'Da thật 100%, sang trọng.', NOW()),
(4, 'Ví da nam cầm tay', 300000, 25, 'images/vi-da.jpg', 'Nâu', 'FreeSize', 'https://www.youtube.com/embed/zYVj1-iPMz0?si=KmYb2f_091-L14-T', 'Nhiều ngăn tiện dụng.', NOW()),
(4, 'Mũ lưỡi trai thời trang', 120000, 60, 'images/mu-luoi-trai.jpg', 'Đen', 'FreeSize', 'https://www.youtube.com/embed/8rYptViyFKg?si=5ZIorEJJ07Tu1-Fb', 'Che nắng, phong cách thể thao.', NOW()),
(4, 'Túi xách nữ thời trang', 550000, 10, 'images/tui-xach.jpg', 'Đen', 'FreeSize', 'https://www.youtube.com/embed/56XMFxKhMg8?si=znVe8dmHIt3AXdeg', 'Phụ kiện không thể thiếu cho phái đẹp.', NOW()),
(4, 'Kính mát thời trang chống UV', 250000, 30, 'images/kinh-mat.jpg', 'Đen', 'FreeSize', 'https://www.youtube.com/embed/tc5yvbyyAzM?si=6cMgwWVMn1NndP4V', 'Bảo vệ mắt khỏi ánh nắng.', NOW()),
(4, 'Vớ (tất) cotton nam nữ', 300000, 100, 'images/tat.jpg', 'Trắng', 'FreeSize', 'https://www.youtube.com/embed/hveaxrI6JTM?si=Hrld4_0-rNVSQtbp', 'Êm chân, thấm hút mồ hôi.', NOW()),
(4, 'Khăn choàng cổ len', 180000, 15, 'images/khan-choang.jpg', 'Xanh', 'FreeSize', 'https://www.youtube.com/embed/hGoBWtIfwko?si=jURDQgoqj0ceYDhb', 'Giữ ấm cổ mùa đông.', NOW()),
(2, 'Quần jean nam Levi''s', 850000, 15, 'images/quan-jean-nam1.jpg', 'Xanh', '29, 30, 31', 'https://www.youtube.com/embed/YVVTN-MwLLs?si=Qjj1SWqdp9KijCX7', 'Quần jean Levi''s chính hãng.', NOW());

-- 4. Chèn một số dữ liệu liên hệ mẫu
-- 4. Chèn một số dữ liệu liên hệ mẫu (Cập nhật SĐT và Chủ đề)
INSERT INTO lien_he (ho_ten, email, sdt, chu_de, noi_dung, anh, ngay_gui) VALUES
('Hoàng Văn Test', 'test@gmail.com', '0912345678', 'Tư vấn mua hàng', 'Sản phẩm áo thun mặc rất thích, shop tư vấn giúp mình size L nhé.', NULL, NOW()),
('Lê Thị Thảo', 'thao@gmail.com', '0987654321', 'Khác', 'Cho mình hỏi quần Jean Levi\'s còn màu sáng không ạ?', 'lienhe_sample.jpg', NOW());

-- 5. Chèn dữ liệu Phần B (Thành viên & Bảng cá nhân)
INSERT INTO thanh_vien (ma_sv, ho_ten, vai_tro_nhom) VALUES
('3120224184', 'Xayyatiew Phoutsana', 'Thành viên nhóm 08')
ON DUPLICATE KEY UPDATE ho_ten = VALUES(ho_ten);

-- Them thong tin thanh vien Giang

INSERT INTO thanh_vien (ma_sv, ho_ten, vai_tro_nhom)
VALUES
('3120224040', 'Ngô Hương Giang', 'Thành viên nhóm 08');

INSERT INTO tv_thoi_khoa_bieu
    (mssv, thu, buoi_sang, buoi_chieu)
VALUES
    ('3120224040', 2, 'Học tập', 'Tự học'),
    ('3120224040', 3, 'Học tập', 'Lập trình'),
    ('3120224040', 4, 'Học tập', 'Làm đồ án'),
    ('3120224040', 5, 'Học tập', 'Lập trình'),
    ('3120224040', 6, 'Học tập', 'Tự học');

INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, cong_nghe) VALUES 
('3120224184', 'Hệ thống Gợi ý Phim', 'Xây dựng thuật toán TF-IDF và Cosine Similarity gợi ý phim', 'HTML, CSS, JS, Python'),
('3120224184', 'Khóa cửa thông minh RFID', 'Nguyên mẫu khóa cửa tự động qua thẻ RFID và Arduino', 'Arduino C, C++, IoT');

INSERT INTO tv_luu_but (mssv, ten, loi_nhan) VALUES 
('3120224184', 'Nguyễn Văn Bạn', 'Trang cá nhân thiết kế rất đẹp và trực quan!');

INSERT INTO thanh_vien (ma_sv, ho_ten, vai_tro_nhom) VALUES 
('3120224182', 'KEOPHOXAY Phaeva', 'Thiết kế giao diện & Hỗ trợ code')
ON DUPLICATE KEY UPDATE ho_ten = VALUES(ho_ten);

INSERT INTO tv_ky_nang (masv, tieu_de, mo_ta, muc) VALUES
('3120224182', 'Lập trình & Đánh dấu', 'HTML5, CSS3, căn bản về Lập trình web.', 'lap-trinh'),
('3120224182', 'Công cụ & Quản lý mã nguồn', 'Sử dụng Git và GitHub để làm việc nhóm.', 'cong-cu'),
('3120224182', 'Kỹ năng mềm', 'Kỹ năng làm việc nhóm, giao tiếp và lên kế hoạch dự án.', 'ky-nang-mem');