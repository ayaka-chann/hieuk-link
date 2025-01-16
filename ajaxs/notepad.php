<?php
require __DIR__ . '/../config.php';

// Hàm tạo chuỗi ngẫu nhiên
function generateRandomString($length = 6)
{
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// Lấy URL hiện tại
function getCurrentUrl()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://")
        . $_SERVER['HTTP_HOST'];
}

// Trả về JSON Response
function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$customCode = isset($_POST['custom_code']) ? trim($_POST['custom_code']) : '';

// Kiểm tra nội dung ghi chú
if (empty($content)) {
    http_response_code(404);
    jsonResponse(['error' => 'Nội dung ghi chú không được để trống!']);
}

// Kiểm tra bí danh hợp lệ
if ($customCode && !preg_match('/^[a-zA-Z0-9]{6,20}$/', $customCode)) {
    http_response_code(404);
    jsonResponse(['error' => 'Bí danh chỉ được chứa chữ và số, độ dài 6-20 ký tự!']);
}

try {
    $shortCode = $customCode ?: generateRandomString();

    // Lưu dữ liệu vào database
    $stmt = $conn->prepare("INSERT INTO notepads (content, short_code) VALUES (?, ?)");
    $stmt->bind_param("ss", $content, $shortCode);

    if (!$stmt->execute()) {
        if ($stmt->errno === 1062) {
            http_response_code(404);
            jsonResponse(['error' => 'Bí danh này đã được sử dụng!']);
        }
        throw new Exception("Database error");
    }

    // Trả về URL rút gọn
    jsonResponse(['shortUrl' => getCurrentUrl() . '/n/' . $shortCode]);
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(404);
    // Hiển thị trang 404 nếu có lỗi hoặc không tìm thấy
    require __DIR__ . '/../pages/404.php';
    exit;
}
