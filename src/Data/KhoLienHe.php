<?php
declare(strict_types=1);

namespace App\Data;

// Nạp tệp kết nối cơ sở dữ liệu
require_once __DIR__ . '/../../inc/db.php';

class KhoLienHe 
{
    public function __construct() {}

    public function them(array $lh): void
    {
        try {
            $pdo = \db();

            // Insert tách bạch từng cột rõ ràng
            $sql = "INSERT INTO lien_he (ho_ten, email, sdt, chu_de, noi_dung, anh, ngay_gui) 
                    VALUES (:ho_ten, :email, :sdt, :chu_de, :noi_dung, :anh, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'ho_ten'   => $lh['hoTen'],
                'email'    => $lh['email'],
                'sdt'      => $lh['sdt'] ?? null,
                'chu_de'   => $lh['chuDe'] ?? null,
                'noi_dung' => $lh['noiDung'],
                'anh'      => $lh['anh']
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Lỗi lưu database: " . $e->getMessage());
        }
    }

    public function tatCa(): array 
    {
        try {
            $pdo = \db();
            $sql = "SELECT * FROM lien_he ORDER BY id DESC";
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }
}