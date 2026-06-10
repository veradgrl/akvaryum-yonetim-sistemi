<?php
session_start();
require_once '../config/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ==========================================
// 1. KAYIT OLMA İŞLEMİ
// ==========================================
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = trim($_POST['ad_soyad']);
    $eposta = trim($_POST['eposta']);
    $unvan = trim($_POST['unvan']);
    $sifre = $_POST['sifre'];

    if (empty($ad_soyad) || empty($eposta) || empty($unvan) || empty($sifre)) {
        header("Location: ../register.php?hata=bos");
        exit;
    }

    //* Şifre password_hash ile hashleniyor
    $hashed_password = password_hash($sifre, PASSWORD_BCRYPT);

    try {
        $sql = "INSERT INTO kullanicilar (ad_soyad, eposta, unvan, sifre) VALUES (:ad_soyad, :eposta, :unvan, :sifre)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ad_soyad' => $ad_soyad,
            ':eposta' => $eposta,
            ':unvan' => $unvan,
            ':sifre' => $hashed_password
        ]);
        
        header("Location: ../login.php?basari=1");
        exit;
    } catch (PDOException $e) {
        header("Location: ../register.php?hata=1");
        exit;
    } 
}

// ==========================================
// 2. LOGIN İŞLEMİ
// ==========================================
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $eposta = trim($_POST['eposta']);
    $sifre = $_POST['sifre'];

    $sql = "SELECT * FROM kullanicilar WHERE eposta = :eposta";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':eposta' => $eposta]);
    $kullanici = $stmt->fetch();
    //* Kullanıcı bulunduysa ve şifre doğrulanırsa oturum değişkenleri atanıyor ve kullanıcı index.php sayfasına yönlendirilir.
    if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
        //* Oturum değişkenleri atılıyor ve kullanıcı index.php'ye yönlendiriliyor (session kontrolü)
        $_SESSION['kullanici_id'] = $kullanici['id'];
        $_SESSION['ad_soyad'] = $kullanici['ad_soyad'];
        $_SESSION['unvan'] = $kullanici['unvan'];
        
        header("Location: ../index.php");
        exit;
    } else { //* Giriş başarısız olduğunda login.php sayfasına hata mesajı ile birlikte yönlendiriliyor
        header("Location: ../login.php?hata=1");
        exit;
    }
}

// ==========================================
// 3. LOGOUT İŞLEMİ
// ==========================================
if ($action === 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../login.php"); //* Çıkış yapıldıktan sonra login.php sayfasına yönlendiriliyor
    exit;
}

?>