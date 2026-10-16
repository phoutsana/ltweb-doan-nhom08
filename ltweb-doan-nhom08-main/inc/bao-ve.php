<?php
if (!isset($_SESSION['user'])) {
    header('Location: dang-nhap.php');
    exit;
}