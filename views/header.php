<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Güvenlik Kontrolü: Giriş yapmamış kullanıcıyı login'e yönlendir
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mavi Dünya | Akvaryum Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .navbar-brand { font-weight: bold; letter-spacing: 1px; }
    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-fish-fins me-2"></i>Mavi Dünya Akvaryumu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigasyon menüsü (Panel ve Yeni Kayıt Ekle) ve oturum bilgisi ile çıkış butonu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fa-solid fa-chart-line me-1"></i> Panel</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add.php"><i class="fa-solid fa-plus-circle me-1"></i> Yeni Takip Kaydı</a>
                </li>
            </ul>

            <!-- Oturum bilgisi ve çıkış butonu -->
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-item text-light me-3">
                    <i class="fa-solid fa-user-tag me-1 text-info"></i> 
                    <strong><?php echo htmlspecialchars($_SESSION['ad_soyad']); ?></strong> 
                    <span class="badge bg-info text-dark ms-1"><?php echo htmlspecialchars($_SESSION['unvan']); ?></span>
                </span>
                <a href="actions/auth.php?action=logout" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Çıkış</a>
            </div>
        </div>
    </div>
</nav>
<div class="container">