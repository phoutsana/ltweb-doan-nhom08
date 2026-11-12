
<?php

// BT7 - Ghi nhat ky su kien bao mat
function bt7_security_log(
    string $event,
    string $path,
    int $status
): void {

    $allowedEvents = [
        'ACCESS_DENIED',
        'CSRF_REJECTED',
        'LOGIN_FAILED',
         'LOGIN_RATE_LIMITED'
    ];

    if (!in_array($event, $allowedEvents, true)) {
        return;
    }

    $entry = [
        'time' => gmdate('c'),
        'event' => $event,
        'status' => $status,
        'path' => substr($path, 0, 200)
    ];

    $json = json_encode(
        $entry,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_INVALID_UTF8_SUBSTITUTE
    );

    if ($json === false) {
        return;
    }

    $logFile = dirname(__DIR__)
        . '/logs/security-audit.jsonl';

    $written = @file_put_contents(
        $logFile,
        $json . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    if ($written === false) {
        error_log('BT7: Cannot write security audit log');
    }
}
