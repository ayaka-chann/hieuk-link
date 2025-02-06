<?php
require __DIR__ . '/../config.php';

// Hàm tạo chuỗi ngẫu nhiên an toàn
function generateRandomString($length = 6)
{
    return bin2hex(random_bytes($length / 2));
}

// Lấy URL hiện tại
function getCurrentUrl()
{
    static $url = null;
    if ($url === null) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'];
    }
    return $url;
}

// Trả về JSON Response
function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Xử lý lỗi và trả về JSON
function handleError($message, $statusCode = 404)
{
    jsonResponse(['error' => $message], $statusCode);
}

$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$customCode = isset($_POST['custom_code']) ? trim($_POST['custom_code']) : '';

// Kiểm tra nội dung ghi chú
if (empty($content)) {
    handleError('Nội dung ghi chú không được để trống!');
}

// Kiểm tra bí danh hợp lệ
if ($customCode && !preg_match('/^[a-zA-Z0-9]{6,20}$/', $customCode)) {
    handleError('Bí danh chỉ được chứa chữ và số, độ dài 6-20 ký tự!');
}

try {
    $shortCode = $customCode ?: generateRandomString();

    // Lưu dữ liệu vào database
    $stmt = $conn->prepare("INSERT INTO notepads (content, short_code) VALUES (?, ?)");
    $stmt->bind_param("ss", $content, $shortCode);

    if (!$stmt->execute()) {
        if ($stmt->errno === 1062) {
            handleError('Bí danh này đã được sử dụng!');
        }
        throw new Exception("Database error");
    }

    // Trả về URL rút gọn
    jsonResponse(['shortUrl' => getCurrentUrl() . '/n/' . $shortCode]);
} catch (Exception $e) {
    error_log($e->getMessage());
    require __DIR__ . '/../pages/404.php';
    exit;
}
