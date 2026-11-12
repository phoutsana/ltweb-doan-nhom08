<?php
declare(strict_types=1);

/**
 * BT7 - Ket noi MySQL theo nguyen tac quyen toi thieu.
 * Mat khau duoc lay tu bien moi truong,
 * khong luu truc tiep trong ma nguon.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // 1. Cau hinh ket noi
    $host = '127.0.0.1';
    $port = 3306;
    $dbName = 'ltweb_nhom08';
    $username = 'bt7_web_nhom08';

    // 2. Doc mat khau tu bien moi truong
    $password = getenv('BT7_DB_PASSWORD');

    if (!is_string($password) || $password === '') {
        throw new RuntimeException(
            'Chua cau hinh mat khau co so du lieu.'
        );
    }

    // 3. DSN su dung utf8mb4
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $host,
        $port,
        $dbName
    );

    // 4. Cau hinh PDO an toan
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    // 5. Ket noi bang tai khoan quyen toi thieu
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        $options
    );

    return $pdo;
}
