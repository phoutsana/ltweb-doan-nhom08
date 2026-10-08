<?php
/**
 * Khai báo hàm escape HTML và khởi tạo phiên dùng chung cho trang PHP.
 * Được nạp bằng require_once từ các trang; kiểm tra qua trang giới thiệu cá nhân.
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE && !session_start()) {
    throw new RuntimeException('Không thể khởi tạo phiên làm việc.');
}

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
