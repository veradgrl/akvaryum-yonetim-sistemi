<?php
session_start();
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Akvaryum Yönetimi - Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Akvaryum Yönetim Sistemi Girişi</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['hata'])): ?>
                        <div class="alert alert-danger">E-posta veya şifre hatalı!</div>
                    <?php endif; ?>
                    <?php if (isset($_GET['basari'])): ?>
                        <div class="alert alert-success">Kayıt başarılı! Şimdi giriş yapabilirsiniz.</div>
                    <?php endif; ?>
                    
                    <form action="actions/auth.php?action=login" method="POST">
                        <div class="mb-3">
                            <label class="form-label">E-posta Adresi</label>
                            <input type="email" name="eposta" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="sifre" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Giriş Yap</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0">Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>