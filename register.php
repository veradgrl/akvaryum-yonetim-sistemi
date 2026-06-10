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
    <title>Akvaryum Yönetimi - Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Akvaryum Personel / Ziyaretçi Kayıt Formu</h4>
                </div>
                <!--Kayıt formu, kullanıcıdan ad soyad, e-posta, unvan ve şifre bilgilerini alır. Kayıt işlemi auth.php dosyasındaki register fonksiyonu tarafından gerçekleştirilir.-->
                <div class="card-body">
                    <?php if (isset($_GET['hata'])): ?>
                        <div class="alert alert-danger">Kayıt oluşturulurken bir hata oluştu veya bu e-posta zaten kayıtlı!</div>
                    <?php endif; ?>
                    <form action="actions/auth.php?action=register" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" name="ad_soyad" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-posta Adresi</label>
                            <input type="email" name="eposta" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sıfat / Görev Tanımı</label>
                            <select name="unvan" class="form-select" required>
                                <option value="Ziyaretçi">Ziyaretçi</option>
                                <option value="Temizlik Personeli">Temizlik Personeli</option>
                                <option value="Veteriner / Bebek Bakım Uzmanı">Veteriner / Bebek Bakım Uzmanı</option>
                                <option value="İlaçlama ve Kimya Sorumlusu">İlaçlama ve Kimya Sorumlusu</option>
                                <option value="Teknik Destek Sorumlusu">Teknik Destek Sorumlusu</option>
                                <option value="Akvaryum Müdürü">Akvaryum Müdürü</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="sifre" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kayıt Ol</button>
                    </form>
                    <hr>
                    <p class="text-center mb-0">Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>