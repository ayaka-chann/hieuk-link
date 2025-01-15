<?php
require 'config.php';

function generateRandomString($length = 6)
{
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

function getCurrentUrl()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $url = filter_var(trim($_POST['url']), FILTER_SANITIZE_URL);
    $customCode = isset($_POST['custom_code']) ? trim($_POST['custom_code']) : '';

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        die(json_encode(['error' => 'URL không hợp lệ!']));
    }

    if ($customCode && !preg_match('/^[a-zA-Z0-9]{3,20}$/', $customCode)) {
        die(json_encode(['error' => 'Bí danh chỉ được chứa chữ và số, độ dài 3-20 ký tự!']));
    }

    try {
        $shortCode = $customCode ?: generateRandomString();
        $stmt = $conn->prepare("INSERT INTO urls (original_url, short_code) VALUES (?, ?) ON DUPLICATE KEY UPDATE id=id");
        $stmt->bind_param("ss", $url, $shortCode);

        if (!$stmt->execute()) {
            if ($stmt->errno === 1062) {
                die(json_encode(['error' => 'Bí danh này đã được sử dụng!']));
            }
            throw new Exception("Lỗi cơ sở dữ liệu!");
        }

        echo json_encode(['shortUrl' => getCurrentUrl() . '/' . $shortCode]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['error' => 'Có lỗi xảy ra, vui lòng thử lại!']);
    }
    exit;
}

if (isset($_GET['code'])) {
    $code = trim($_GET['code']);
    $stmt = $conn->prepare("SELECT original_url FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->bind_result($originalUrl);

    if ($stmt->fetch()) {
        header("Location: " . $originalUrl);
        exit;
    }

    require '404.php';
    exit;
}
