<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: ../login.php");
    exit;
}

//* Güvenlik Duvarı: Ekleme, Güncelleme ve Silme istekleri için unvan kontrolü -- ziyaretçi ve temizlik personeli bu işlemleri yapamaz.
if (isset($_GET['action'])) {
    $yetkisiz_unvanlar = ['Ziyaretçi', 'Temizlik Personeli'];
    if (in_array($_SESSION['unvan'], $yetkisiz_unvanlar)) {
        header("Location: ../index.php?hata=yetkisiz");
        exit;
    }
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ==========================================
// 1. KAYIT EKLEME (CREATE)
// ==========================================
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') { //* Yeni kayıt ekleme işlemi
    $tur_adi = trim($_POST['tur_adi']);
    $bolum_adi = trim($_POST['bolum_adi']); 
    $tank_numarasi = trim($_POST['tank_numarasi']);
    $saglik_durumu = $_POST['saglik_durumu'];
    $canli_sayisi = intval($_POST['canli_sayisi']);
    $son_yemleme = !empty($_POST['son_yemleme']) ? $_POST['son_yemleme'] : null;
    $son_bakim_notu = trim($_POST['son_bakim_notu']);
    
    $sorumlu_personel = $_SESSION['ad_soyad'] . " (" . $_SESSION['unvan'] . ")";

    //* Form verilerinde zorunlu alanların boş olup olmadığı kontrol edilir. Eğer boş alan varsa kullanıcı ekleme sayfasına hata mesajı ile yönlendirilir.
    if (empty($tur_adi) || empty($bolum_adi) || empty($tank_numarasi)) {
        header("Location: ../add.php?hata=bos");
        exit;
    }

    //* Veritabanına yeni kayıt ekleme işlemi
    try { 
        $sql = "INSERT INTO akvaryum_takip (tur_adi, bolum_adi, tank_numarasi, saglik_durumu, canli_sayisi, son_yemleme, son_bakim_notu, sorumlu_personel) 
                VALUES (:tur_adi, :bolum_adi, :tank_numarasi, :saglik_durumu, :canli_sayisi, :son_yemleme, :son_bakim_notu, :sorumlu_personel)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tur_adi' => $tur_adi,
            ':bolum_adi' => $bolum_adi,
            ':tank_numarasi' => $tank_numarasi,
            ':saglik_durumu' => $saglik_durumu,
            ':canli_sayisi' => $canli_sayisi,
            ':son_yemleme' => $son_yemleme,
            ':son_bakim_notu' => $son_bakim_notu,
            ':sorumlu_personel' => $sorumlu_personel
        ]);

        header("Location: ../index.php?basari=eklendi");
        exit;
        //* Kayıt ekleme işlemi başarılı olduğunda kullanıcı index.php sayfasına yönlendiriliyor
    } catch (PDOException $e) {
        die("Veri eklenirken hata oluştu: " . $e->getMessage());
    }
}

// ==========================================
// 2. KAYIT GÜNCELLEME (UPDATE)
// ==========================================
//* Düzenleme işlemi için edit.php'den gelen POST verileri alınır ve güncelleme yapılır
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') { //* Kayıt güncelleme işlemi
    $id = intval($_POST['id']);
    $tur_adi = trim($_POST['tur_adi']);
    $bolum_adi = trim($_POST['bolum_adi']);
    $tank_numarasi = trim($_POST['tank_numarasi']);
    $saglik_durumu = $_POST['saglik_durumu'];
    $canli_sayisi = intval($_POST['canli_sayisi']);
    $son_yemleme = !empty($_POST['son_yemleme']) ? $_POST['son_yemleme'] : null;
    $son_bakim_notu = trim($_POST['son_bakim_notu']);
    
    //* Düzenleyen personelin bilgisi ve unvanı güncelleme notuna eklenir
    $sorumlu_personel = $_SESSION['ad_soyad'] . " (" . $_SESSION['unvan'] . ")";

    //* Form verilerinde zorunlu alanların boş olup olmadığı kontrol edilir. Eğer boş alan varsa kullanıcı düzenleme sayfasına hata mesajı ile yönlendirilir.
    if (empty($id) || empty($tur_adi) || empty($bolum_adi) || empty($tank_numarasi)) {
        header("Location: ../edit.php?id=$id&hata=bos");
        exit;
    }

    try { //* Veritabanında ilgili kaydı güncelleme işlemi
        $sql = "UPDATE akvaryum_takip SET 
                    tur_adi = :tur_adi, 
                    bolum_adi = :bolum_adi, 
                    tank_numarasi = :tank_numarasi, 
                    saglik_durumu = :saglik_durumu, 
                    canli_sayisi = :canli_sayisi, 
                    son_yemleme = :son_yemleme, 
                    son_bakim_notu = :son_bakim_notu,
                    sorumlu_personel = :sorumlu_personel
                WHERE id = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tur_adi' => $tur_adi,
            ':bolum_adi' => $bolum_adi,
            ':tank_numarasi' => $tank_numarasi,
            ':saglik_durumu' => $saglik_durumu,
            ':canli_sayisi' => $canli_sayisi,
            ':son_yemleme' => $son_yemleme,
            ':son_bakim_notu' => $son_bakim_notu,
            ':sorumlu_personel' => $sorumlu_personel,
            ':id' => $id
        ]);

        header("Location: ../index.php?basari=guncellendi");
        exit;
    } catch (PDOException $e) {
        die("Veri güncellenirken hata oluştu: " . $e->getMessage());
    }
}

// ==========================================
// 3. KAYIT SİLME (DELETE)
// ==========================================
//* Silme işlemi için index.php'den gelen ID'ye göre kayıt silinir
if ($action === 'delete') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    //* Geçerli bir ID olup olmadığı kontrol edilir. Geçersiz ID durumunda kullanıcı index.php sayfasına hata mesajı ile yönlendirilir.
    if ($id <= 0) {
        header("Location: ../index.php?hata=gecersiz_id");
        exit;
    }
    
    //* Veritabanında ilgili kaydı silme işlemi
    try {
        $sql = "DELETE FROM akvaryum_takip WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: ../index.php?basari=silindi");
        exit;
    } catch (PDOException $e) {
        die("Veri silinirken hata oluştu: " . $e->getMessage());
    }
}

?>