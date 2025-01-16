<?php
$title = 'HiếuK';
include_once 'pages/header.php';
?>

<div class="container">
    <main class="main-content">
        <h1><?= htmlspecialchars($title) ?></h1>
        <div class="action-buttons">
            <button><a href="/rutgonlink" class="home-button">Rút gọn URL</a></button>
            <button><a href="/notepad" class="home-button">HK Notepad</a></button>
        </div>
    </main>
</div>