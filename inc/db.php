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

    // BT7 - Doc cau hinh CSDL tu tep .env
$host = (string) ($_ENV['DB_HOST'] ?? '');

$port = filter_var(
    $_ENV['DB_PORT'] ?? '3306',
    FILTER_VALIDATE_INT
);

$dbName = (string) ($_ENV['DB_DATABASE'] ?? '');

$username = (string) ($_ENV['DB_USERNAME'] ?? '');

// Cho phep mat khau rong voi root tren XAMPP
$password = (string) ($_ENV['DB_PASSWORD'] ?? '');

// Kiem tra cau hinh bat buoc
if ($host === '' || $dbName === '' || $username === '') {
    throw new RuntimeException(
        'Thieu cau hinh ket noi co so du lieu.'
    );
}

if ($port === false || $port < 1 || $port > 65535) {
    throw new RuntimeException(
        'Cong ket noi CSDL khong hop le.'
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
