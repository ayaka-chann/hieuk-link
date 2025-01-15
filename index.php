<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rút gọn URL</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Rút gọn URL</h1>
        <form id="shortenForm">
            <input type="url" name="url" placeholder="Nhập URL cần rút gọn" required>
            <button type="button" onclick="document.getElementById('customAlias').classList.toggle('hidden')">
                Tùy chỉnh Bí danh
            </button>
            <div id="customAlias" class="hidden">
                <input type="text" name="custom_code" placeholder="Nhập Bí danh (3-20 ký tự)">
            </div>
            <button type="submit">Rút gọn ngay</button>
        </form>
        <div class="result"></div>
    </div>
    <script>
        document.getElementById('shortenForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const resultDiv = document.querySelector('.result');

            try {
                const response = await fetch('shorten.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.error,
                        heightAuto: false,
                        scrollbarPadding: false
                    });
                    return;
                }

                resultDiv.innerHTML = `
                <p>Link rút gọn của bạn: <a href="${data.shortUrl}" target="_blank">${data.shortUrl}</a></p>
                <button onclick="navigator.clipboard.writeText('${data.shortUrl}').then(() => Swal.fire({
                    icon: 'success',
                    title: 'Đã sao chép!',
                    timer: 1500,
                    heightAuto: false,
                    scrollbarPadding: false
                }))">Sao chép</button>
            `;
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    text: 'Không thể kết nối đến máy chủ!',
                    heightAuto: false,
                    scrollbarPadding: false
                });
            }
        };
    </script>
</body>

</html>