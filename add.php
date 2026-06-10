<?php 
session_start();
// Yetkisiz unvanların tanımlanması
$yetkisiz_unvanlar = ['Ziyaretçi', 'Temizlik Personeli'];

if (isset($_SESSION['unvan']) && in_array($_SESSION['unvan'], $yetkisiz_unvanlar)) {
    header("Location: index.php?hata=yetkisiz");
    exit;
}

require_once 'views/header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-circle-plus me-2"></i>Yeni Canlı & Bakım Kaydı Ekle</h5>
            </div>
            <!-- Yeni kayıt ekleme formu -->
            <div class="card-body p-4">
                <form action="actions/crud.php?action=create" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Canlı / Balık Türü</label>
                        <input type="text" name="tur_adi" class="form-control" placeholder="Örn: Palyaço Balığı" required>
                    </div>

                    <!-- Akvaryum bölümü ve tank numarası için yan yana iki sütunlu form grubu -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Akvaryum Bölümü</label>
                            <select name="bolum_adi" class="form-select" required>
                                <option value="" disabled selected>Bölüm Seçiniz...</option>
                                <option value="Ana Resif Tankı">Ana Resif Tankı (Mercanlar & Tropikal)</option>
                                <option value="Köpekbalığı Krallığı">Köpekbalığı Krallığı (Açık Deniz)</option>
                                <option value="Amazon Yağmur Ormanları">Amazon Yağmur Ormanları (Tatlı Su)</option>
                                <option value="Kutup Tüneli">Kutup Tüneli (Penguenler & Soğuk Deniz)</option>
                                <option value="Deniz Anası Galerisi">Deniz Anası Galerisi (Özel Aydınlatmalı)</option>
                                <option value="Dokunma ve Etkileşim Havuzu">Dokunma ve Etkileşim Havuzu (Vatozlar)</option>
                                <option value="Karantina ve Bebek Bakım Ünitesi">Karantina ve Yeni Doğan Bakım Ünitesi</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tank Numarası / Kodu</label>
                            <input type="text" name="tank_numarasi" class="form-control" placeholder="Örn: T-104, B-12" required>
                        </div>
                    </div>

                    <!-- Sağlık durumu ve canlı sayısı için yan yana iki sütunlu form grubu -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sağlık Durumu</label>
                            <select name="saglik_durumu" class="form-select" required>
                                <option value="Sağlıklı">Sağlıklı</option>
                                <option value="Karantinada">Karantinada</option>
                                <option value="Yeni Doğum">Yeni Doğum</option>
                                <option value="Tedavi Altında">Tedavi Altında</option>
                                <option value="Ölüm Kaydı (Temizlik Gerekli)">Ölüm Kaydı (Temizlik Gerekli)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Canlı Sayısı</label>
                            <input type="number" name="canli_sayisi" class="form-control" min="0" value="1" required>
                        </div>
                    </div>

                    <!-- Son yemleme/bakım zamanı ve yapılan işlem notları için form grubu -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Son Yemleme / Bakım Zamanı</label>
                        <input type="datetime-local" name="son_yemleme" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>

                    <!-- Yapılan işlem ve bakım notları için geniş bir textarea alanı -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Yapılan İşlem ve Bakım Notları</label>
                        <textarea name="son_bakim_notu" class="form-control" rows="4" placeholder="Doğum detayları, temizlik durumu, su sıcaklığı, teknik arıza veya ilaçlama notlarını buraya yazınız..."></textarea>
                    </div>

                    <!-- Formun alt kısmında geri dönme ve kaydetme butonları -->
                    <div class="d-flex justify-content-between pt-3">
                        <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> İptal Et</a>
                        <button type="submit" class="btn btn-success px-4"><i class="fa-solid fa-save me-1"></i> Kaydı Sisteme İşle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>