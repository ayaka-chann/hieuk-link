<?php
$title = 'HiếuK';
include_once 'pages/header.php';
?>

<div class="container">
    <main class="main-content">
        <h1><?= htmlspecialchars($title) ?></h1>
        <div class="action-buttons">
            <button class="home-button" onclick="window.location.href='/rutgonlink'">Rút gọn URL</button>
            <button class="home-button" onclick="window.location.href='/notepad'">HK Notepad</button>
        </div>
    </main>
</div>