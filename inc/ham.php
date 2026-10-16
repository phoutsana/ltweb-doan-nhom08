
<?php

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

function vnd(int|float $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' ₫';
}