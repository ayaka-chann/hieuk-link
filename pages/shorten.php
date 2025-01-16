<?php
$title = 'Rút gọn URL';
include_once 'header.php';
?>

<div class="container">
    <h1>Rút gọn URL</h1>
    <form id="shortenForm">
        <input type="url" name="url" placeholder="Nhập URL cần rút gọn" required>
        <button type="button" onclick="toggleAlias()">Tùy chỉnh Bí danh</button>
        <div id="customAlias" class="hidden">
            <input type="text" name="custom_code" placeholder="Nhập Bí danh (3-20 ký tự)">
        </div>
        <button type="submit">Rút gọn ngay</button>
    </form>
    <div class="result"></div>
    <br>
    <div class="action-buttons">
        <button><a href="/" class="home-button">Quay về trang chủ</a></button>
    </div>
</div>

<script>
    const toggleAlias = () => {
        document.getElementById('customAlias').classList.toggle('hidden');
    };

    document.getElementById('shortenForm').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const resultDiv = document.querySelector('.result');

        try {
            const response = await fetch('/ajaxs/shorten.php', {
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
                <p>Link rút gọn của bạn: <a href="${data.shortUrl}" target="_blank">${data.shortUrl}</a></p>
                <button onclick="copyToClipboard('${data.shortUrl}')">Sao chép</button>
            `;
        } catch {
            Swal.fire({
                icon: 'error',
                text: 'Không thể kết nối đến máy chủ!',
                heightAuto: false,
            });
        }
    };

    const copyToClipboard = (url) => {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Đã sao chép!',
                timer: 1500,
                heightAuto: false,
            });
        });
    };
</script>