<?php
// Bỏ qua config ở đây vì file này được gọi khi hệ thống đã sập
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>500 - Lỗi máy chủ</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding-top: 100px;
            background: #f3f4f6;
            color: #333;
        }

        h1 {
            font-size: 3rem;
            color: #dc2626;
        }
    </style>
</head>

<body>
    <h1>500 - Lỗi hệ thống</h1>
    <p>Hệ thống đang gặp sự cố. Vui lòng quay lại sau!</p>
</body>

</html>