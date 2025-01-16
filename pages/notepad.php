<?php
$title = 'HK Notepad';
include_once 'header.php';

if (isset($_GET['code'])) {
    $code = trim($_GET['code']);
    // Kiểm tra độ dài và ký tự hợp lệ của short_code
    if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $code)) {
        require '404.php';
        exit;
    }

    // Truy vấn cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT content FROM notepads WHERE short_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->bind_result($content);

    if ($stmt->fetch()) {
        $stmt->close();
?>
        <div class="container container-notepad">
            <div class="notepad-view">
                <textarea class="view-content" readonly><?= htmlspecialchars($content) ?></textarea>
                <div class="action-buttons">
                    <button><a href="/" class="home-button">Quay về trang chủ</a></button>
                </div>
            </div>
        </div>
<?php
    } else {
        $stmt->close();
        require '404.php';
    }
    exit;
}
?>

<div class="container container-notepad">
    <h1>HK Notepad</h1>
    <form id="notepadForm">
        <textarea name="content" placeholder="Nhập ghi chú của bạn..." required></textarea>
        <button type="button" onclick="toggleAlias()">Tùy chỉnh Bí danh</button>
        <div id="customAlias" class="hidden">
            <input type="text" name="custom_code" placeholder="Nhập Bí danh (3-20 ký tự)">
        </div>
        <button type="submit">Lưu Ghi Chú</button>
    </form>
    <div class="result"></div>
    <br>
    <div class="action-buttons">
        <button><a href="/" class="home-button">Quay về trang chủ</a></button>
    </div>
</div>

<script>
    // Toggle hiển thị tùy chỉnh bí danh
    const toggleAlias = () => {
        document.getElementById('customAlias').classList.toggle('hidden');
    };

    // Xử lý form gửi
    document.getElementById('notepadForm').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const resultDiv = document.querySelector('.result');

        try {
            const response = await fetch('/ajaxs/notepad.php', {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: data.error,
                    heightAuto: false,
                });
                return;
            }

            resultDiv.innerHTML = `
                <p>Ghi chú của bạn đã được lưu tại: <a href="${data.shortUrl}" target="_blank">${data.shortUrl}</a></p>
                <button onclick="copyToClipboard('${data.shortUrl}')">Sao chép Link</button>
            `;
        } catch {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Không thể kết nối đến máy chủ!',
                heightAuto: false,
            });
        }
    };

    // Sao chép URL vào clipboard
    const copyToClipboard = (url) => {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Đã sao chép link!',
                timer: 1500,
                heightAuto: false,
            });
        });
    };
</script>