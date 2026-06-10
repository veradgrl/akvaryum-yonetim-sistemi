<?php 
session_start();
//* Yetkisiz unvanların tanımlanması
$yetkisiz_unvanlar = ['Ziyaretçi', 'Temizlik Personeli'];

//* Eğer oturum açmış kullanıcı varsa ve unvanı yetkisiz unvanlar arasında ise index.php sayfasına yetkisizolduğu hata mesajı ile yönlendirilir. 
if (isset($_SESSION['unvan']) && in_array($_SESSION['unvan'], $yetkisiz_unvanlar)) {
    header("Location: index.php?hata=yetkisiz");
    exit;
}
require_once 'config/db.php';
require_once 'views/header.php'; 

//* URL'den gelen ID alınır ve tamsayıya çevrilir. Geçerli bir ID olup olmadığı kontrol edilir. Geçersiz ID durumunda kullanıcı index.php sayfasına hata mesajı ile yönlendirilir.
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM akvaryum_takip WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$kayit = $stmt->fetch();

if (!$kayit) {
    echo '<div class="alert alert-danger">Düzenlenmek istenen kayıt bulunamadı!</div>';
    require_once 'views/footer.php';
    exit;
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Akvaryum Kaydını Düzenle (ID: #<?php echo $kayit['id']; ?>)</h5>
            </div>
            <div class="card-body p-4">
                <form action="actions/crud.php?action=update" method="POST">
                    <input type="hidden" name="id" value="<?php echo $kayit['id']; ?>">

                    <!-- Canlı türü için metin girişi alanı -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Canlı / Balık Türü</label>
                        <input type="text" name="tur_adi" class="form-control" value="<?php echo htmlspecialchars($kayit['tur_adi']); ?>" required>
                    </div>
                    <!-- Akvaryum bölümü ve tank numarası için yan yana iki sütunlu form grubu -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Akvaryum Bölümü</label>
                            <select name="bolum_adi" class="form-select" required>
                                <option value="Ana Resif Tankı" <?php echo $kayit['bolum_adi'] === 'Ana Resif Tankı' ? 'selected' : ''; ?>>Ana Resif Tankı (Mercanlar & Tropikal)</option>
                                <option value="Köpekbalığı Krallığı" <?php echo $kayit['bolum_adi'] === 'Köpekbalığı Krallığı' ? 'selected' : ''; ?>>Köpekbalığı Krallığı (Açık Deniz)</option>
                                <option value="Amazon Yağmur Ormanları" <?php echo $kayit['bolum_adi'] === 'Amazon Yağmur Ormanları' ? 'selected' : ''; ?>>Amazon Yağmur Ormanları (Tatlı Su)</option>
                                <option value="Kutup Tüneli" <?php echo $kayit['bolum_adi'] === 'Kutup Tüneli' ? 'selected' : ''; ?>>Kutup Tüneli (Penguenler & Soğuk Deniz)</option>
                                <option value="Deniz Anası Galerisi" <?php echo $kayit['bolum_adi'] === 'Deniz Anası Galerisi' ? 'selected' : ''; ?>>Deniz Anası Galerisi (Özel Aydınlatmalı)</option>
                                <option value="Dokunma ve Etkileşim Havuzu" <?php echo $kayit['bolum_adi'] === 'Dokunma ve Etkileşim Havuzu' ? 'selected' : ''; ?>>Dokunma ve Etkileşim Havuzu (Vatozlar)</option>
                                <option value="Karantina ve Bebek Bakım Ünitesi" <?php echo $kayit['bolum_adi'] === 'Karantina ve Bebek Bakım Ünitesi' ? 'selected' : ''; ?>>Karantina ve Bebek Bakım Ünitesi</option>
                            </select>
                        </div>
                        
                        <!-- Tank numarası için metin girişi alanı -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tank Numarası / Kodu</label>
                            <input type="text" name="tank_numarasi" class="form-control" value="<?php echo htmlspecialchars($kayit['tank_numarasi']); ?>" required>
                        </div>
                    </div>

                    <!-- Sağlık durumu ve canlı sayısı için yan yana iki sütunlu form grubu -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sağlık Durumu</label>
                            <select name="saglik_durumu" class="form-select" required>
                                <option value="Sağlıklı" <?php echo $kayit['saglik_durumu'] === 'Sağlıklı' ? 'selected' : ''; ?>>Sağlıklı</option>
                                <option value="Karantinada" <?php echo $kayit['saglik_durumu'] === 'Karantinada' ? 'selected' : ''; ?>>Karantinada (Yeni Doğum / Gözlem)</option>
                                <option value="Tedavi Altında" <?php echo $kayit['saglik_durumu'] === 'Tedavi Altında' ? 'selected' : ''; ?>>Tedavi Altında</option>
                                <option value="Ölüm Kaydı (Temizlik Gerekli)" <?php echo $kayit['saglik_durumu'] === 'Ölüm Kaydı (Temizlik Gerekli)' ? 'selected' : ''; ?>>Ölüm Kaydı (Temizlik Gerekli)</option>
                            </select>
                        </div>
                        <!-- Canlı sayısı için sayı girişi alanı -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Canlı Sayısı</label>
                            <input type="number" name="canli_sayisi" class="form-control" min="0" value="<?php echo $kayit['canli_sayisi']; ?>" required>
                        </div>
                    </div>

                    <!-- Son yemleme/bakım zamanı ve yapılan işlem notları için form grubu -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Son Yemleme / Bakım Zamanı</label>
                        <input type="datetime-local" name="son_yemleme" class="form-control" value="<?php echo $kayit['son_yemleme'] ? date('Y-m-d\TH:i', strtotime($kayit['son_yemleme'])) : date('Y-m-d\TH:i'); ?>">
                    </div>

                    <!-- Yapılan işlem ve bakım notları için geniş bir textarea alanı -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Yapılan İşlem ve Bakım Notları</label>
                        <textarea name="son_bakim_notu" class="form-control" rows="4"><?php echo htmlspecialchars($kayit['son_bakim_notu']); ?></textarea>
                    </div>

                    <!-- Formun alt kısmında geri dönme ve kaydetme butonları -->
                    <div class="d-flex justify-content-between pt-3">
                        <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Geri Dön</a>
                        <button type="submit" class="btn btn-warning fw-bold px-4"><i class="fa-solid fa-rotate me-1"></i> Değişiklikleri Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>