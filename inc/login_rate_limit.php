<?php
declare(strict_types=1);

// BT7: Gioi han dang nhap sai theo IP
// 5 lan sai trong 15 phut

function bt7_login_rate_limit(
    bool $recordFailure = false
): array {

    $dir = sys_get_temp_dir() . '/bt7-login-rate-nhom08';


    if (
        !is_dir($dir) &&
        !mkdir($dir, 0700, true) &&
        !is_dir($dir)
    ) {
        throw new RuntimeException(
            'Khong tao duoc thu muc rate limit'
        );
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $file = $dir . '/' .
        hash('sha256', $ip) . '.json';

    $handle = fopen($file, 'c+');

    if ($handle === false) {
        throw new RuntimeException(
            'Khong mo duoc bo dem dang nhap'
        );
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException(
                'Khong khoa duoc bo dem'
            );
        }

        rewind($handle);

        $raw = stream_get_contents($handle);

        $times = json_decode(
            $raw === '' ? '[]' : $raw,
            true
        );

        if (!is_array($times)) {
            $times = [];
        }

        $now = time();
        $window = 900;
        $maxAttempts = 5;

        // Chi giu cac lan sai trong 15 phut
        $times = array_values(array_filter(
            $times,
            static fn($t) =>
                is_int($t) &&
                $t > $now - $window &&
                $t <= $now
        ));

        sort($times);

        $blocked = count($times) >= $maxAttempts;

        if ($recordFailure && !$blocked) {
            $times[] = $now;
        }

        $retryAfter = $blocked
            ? max(1, $times[0] + $window - $now)
            : 0;

        $json = json_encode(
            $times,
            JSON_THROW_ON_ERROR
        );

        rewind($handle);
        ftruncate($handle, 0);

        if (fwrite($handle, $json) !== strlen($json)) {
            throw new RuntimeException(
                'Khong ghi duoc bo dem'
            );
        }

        fflush($handle);

        return [
            'blocked' => $blocked,
            'attempts' => count($times),
            'retry_after' => $retryAfter
        ];

    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
