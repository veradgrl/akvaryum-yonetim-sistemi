<?php
require_once 'config/db.php';
require_once 'views/header.php';

// Veritabanındaki tüm akvaryum kayıtlarını en yeni tarihten eskiye doğru sıralanır
try {
    $sql = "SELECT * FROM akvaryum_takip ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $kayitlar = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Veri çekme hatası: " . $e->getMessage());
}
?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-clipboard-list text-primary me-2"></i>Akvaryum Takip Paneli</h2>
    <?php if ($_SESSION['unvan'] !== 'Ziyaretçi' && $_SESSION['unvan'] !== 'Temizlik Personeli'): ?>
        <a href="add.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Yeni Kayıt Ekle</a>
    <?php endif; ?>
</div>

<?php if (isset($_GET['basari'])): ?>
    <?php if ($_GET['basari'] === 'eklendi'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> Yeni kayıt başarıyla sisteme eklendi!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['basari'] === 'guncellendi'): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-info me-2"></i> Kayıt başarıyla güncellendi.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <!-- Kayıt güncelleme işlemi başarılı olduğunda kullanıcıya bilgi mesajı gösterilir -->
    <?php elseif ($_GET['basari'] === 'silindi'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-trash-can me-2"></i> Kayıt sistemden silindi.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- Eğer kullanıcı yetkisiz bir işlem yapmaya çalışırsa hata mesajı gösterilir -->
    <?php if (isset($_GET['hata']) && $_GET['hata'] === 'yetkisiz'): ?> 
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Yetkisiz Erişim!</strong> Bu işlemi gerçekleştirmek için yetkiniz bulunmamaktadır.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php endif; ?>

<!-- Akvaryum kayıtlarının listelendiği tablo yapısı -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
    <tr>
        <th scope="col" class="ps-3">ID</th>
        <th scope="col">Canlı Türü</th>
        <th scope="col">Akvaryum Bölümü</th>
        <th scope="col">Tank No</th>
        <th scope="col">Adet</th>
        <th scope="col">Sağlık Durumu</th>
        <th scope="col">Son İşlem / Bakım Zamanı</th>
        <th scope="col">Sorumlu Personel</th>
        <?php if ($_SESSION['unvan'] !== 'Ziyaretçi' && $_SESSION['unvan'] !== 'Temizlik Personeli'): ?>
            <th scope="col" class="text-center pe-3">İşlemler</th>
        <?php endif; ?>
    </tr>
</thead>
                <tbody>
                    <?php if (count($kayitlar) > 0): ?>
                        <?php foreach ($kayitlar as $kayit): 
                            // Sağlık durumuna göre dinamik Bootstrap renk sınıfları belirliyoruz
                            $badge_class = 'bg-success';
                            if ($kayit['saglik_durumu'] === 'Karantinada') $badge_class = 'bg-warning text-dark';
                            if ($kayit['saglik_durumu'] === 'Tedavi Altında') $badge_class = 'bg-info text-dark';
                            if ($kayit['saglik_durumu'] === 'Ölüm Kaydı (Temizlik Gerekli)') $badge_class = 'bg-danger';
                        ?>
                            <tr>
                                <th scope="row" class="ps-3"><?php echo $kayit['id']; ?></th>
                                <td><strong><?php echo htmlspecialchars($kayit['tur_adi']); ?></strong></td>
                                <td><?php echo htmlspecialchars($kayit['bolum_adi']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($kayit['tank_numarasi']); ?></span></td>
                                <td><?php echo $kayit['canli_sayisi']; ?></td>
                                <td><span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($kayit['saglik_durumu']); ?></span></td>
                                <td>
                                    <?php 
                                    echo $kayit['son_yemleme'] 
                                        ? date('d.m.Y H:i', strtotime($kayit['son_yemleme'])) 
                                        : '<span class="text-muted">Girilmedi</span>'; 
                                    ?>
                                </td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($kayit['sorumlu_personel']); ?></small></td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($kayit['sorumlu_personel']); ?></small></td>

<!-- Kayıt düzenleme ve silme işlemleri sadece yetkili kullanıcılar tarafından yapılabilir. Ziyaretçi ve temizlik personeli bu işlemleri gerçekleştiremez. -->
<?php if ($_SESSION['unvan'] !== 'Ziyaretçi' && $_SESSION['unvan'] !== 'Temizlik Personeli'): ?>
    <td class="text-center pe-3">
        <div class="btn-group btn-group-sm" role="group">
            <a href="edit.php?id=<?php echo $kayit['id']; ?>" class="btn btn-outline-warning" title="Düzenle">
                <i class="fa-solid fa-pen"></i>
            </a>
            <a href="actions/crud.php?action=delete&id=<?php echo $kayit['id']; ?>" 
               class="btn btn-outline-danger" 
               onclick="return confirm('Bu akvaryum kaydını silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');" 
               title="Sil">
                <i class="fa-solid fa-trash"></i>
            </a>
        </div>
    </td>
<?php endif; ?>
                            </tr>
                            <?php if (!empty($kayit['son_bakim_notu'])): ?>
                                <tr class="table-light">
                                    <td colspan="9" class="ps-4 py-1 text-muted">
                                        <small><i class="fa-solid fa-comment-medical me-1"></i> <strong>Bakım/Teknik Notu:</strong> <?php echo htmlspecialchars($kayit['son_bakim_notu']); ?></small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-fish fa-2x mb-2 d-block"></i>
                                Henüz sisteme girilmiş bir akvaryum takip kaydı bulunmuyor.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>