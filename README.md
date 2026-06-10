


# 🐠 Akvaryum Yönetim Sistemi
 
> **Canlı Demo:** [http://95.130.171.20/~st23360859003/index.php](http://95.130.171.20/~st23360859003/index.php)
 
---
 
## 📋 Proje Hakkında
 
**Akvaryum Yönetim Sistemi**, bir akvaryumun canlı takibini, bakım süreçlerini ve personel yönetimini dijital ortamda kolaylaştırmak için geliştirilmiş bir web uygulamasıdır. Sistem; farklı bölümlerdeki tankları, canlı türlerini, sağlık durumlarını ve yapılan bakım işlemlerini merkezi bir panel üzerinden yönetmeye olanak tanır.
 
---

### 📝 Kayıt Ekranı
<img width="722" height="612" alt="Ekran görüntüsü 2026-06-10 122526" src="https://github.com/user-attachments/assets/ad965a53-a61a-407f-bd88-d9e69ac36706" />
 
---

### 🔑 Giriş Ekranı
<img width="578" height="396" alt="Ekran görüntüsü 2026-06-10 130651" src="https://github.com/user-attachments/assets/68d07142-b343-4121-88eb-3c02b4deb111" />
 
---
 
### 📊 Ana Takip Paneli
<img width="1327" height="827" alt="Ekran görüntüsü 2026-06-10 130801" src="https://github.com/user-attachments/assets/0ec614e5-b045-4202-b180-1d53cee26a41" />

---
 
### ➕ Yeni Kayıt Ekleme
<img width="1157" height="823" alt="Ekran görüntüsü 2026-06-10 130732" src="https://github.com/user-attachments/assets/d9590911-c579-4002-a3d1-5062d039435e" />
 
---
 
### ✏️ Kayıt Düzenleme

<img width="1345" height="831" alt="Ekran görüntüsü 2026-06-10 130813" src="https://github.com/user-attachments/assets/d3b7614e-a7fe-4069-873a-f5d0f25a3100" />
 
---
 
## ✨ Özellikler
 
- 🔐 **Kullanıcı Kimlik Doğrulama** — Güvenli giriş ve kayıt sistemi (oturum tabanlı)
- 👥 **Rol Tabanlı Yetkilendirme** — Farklı unvanlara göre kısıtlı erişim kontrolü
- 📊 **Canlı Takip Paneli** — Tüm akvaryum kayıtlarını tek ekranda listeler
- ➕ **Kayıt Ekleme** — Yeni canlı ve bakım kaydı oluşturma
- ✏️ **Kayıt Düzenleme** — Mevcut kayıtları güncelleme
- 🗑️ **Kayıt Silme** — Kayıt kaldırma (onay mekanizmalı)
- 🏷️ **Sağlık Durumu Badgeleri** — Renk kodlu sağlık durumu gösterimi (Sağlıklı / Karantinada / Tedavi Altında / Ölüm Kaydı)
- 📝 **Bakım Notları** — Her kayıda notlar ve teknik açıklamalar eklenebilir
---
 
## 👤 Kullanıcı Rolleri ve Yetkiler
 
| Unvan | Kayıt Listesi | Kayıt Ekle | Kayıt Düzenle | Kayıt Sil |
|-------|:---:|:---:|:---:|:---:|
| Akvaryum Müdürü | ✅ | ✅ | ✅ | ✅ |
| Veteriner / Bebek Bakım Uzmanı | ✅ | ✅ | ✅ | ✅ |
| İlaçlama ve Kimya Sorumlusu | ✅ | ✅ | ✅ | ✅ |
| Teknik Destek Sorumlusu | ✅ | ✅ | ✅ | ✅ |
| Temizlik Personeli | ✅ | ❌ | ❌ | ❌ |
| Ziyaretçi | ✅ | ❌ | ❌ | ❌ |
 
---
 
## 🏢 Akvaryum Bölümleri
 
Sistemde tanımlanmış bölümler:
 
- 🪸 **Ana Resif Tankı** — Mercanlar & Tropikal Balıklar
- 🦈 **Köpekbalığı Krallığı** — Açık Deniz
- 🌿 **Amazon Yağmur Ormanları** — Tatlı Su
- 🧊 **Kutup Tüneli** — Penguenler & Soğuk Deniz
- 🌊 **Deniz Anası Galerisi** — Özel Aydınlatmalı
- 🐟 **Dokunma ve Etkileşim Havuzu** — Vatozlar
- 🏥 **Karantina ve Yeni Doğan Bakım Ünitesi**
---
 
## 🗂️ Proje Dosya Yapısı
 
```
akvaryum-yonetim/
│
├── index.php               # Ana panel — kayıt listesi
├── add.php                 # Yeni kayıt ekleme formu
├── edit.php                # Kayıt düzenleme formu
├── login.php               # Giriş sayfası
├── register.php            # Kayıt olma sayfası
│
├── actions/
│   ├── auth.php            # Giriş / kayıt işlemleri
│   └── crud.php            # Oluştur / Güncelle / Sil işlemleri
│
├── config/
│   └── db.php              # Veritabanı bağlantısı (PDO)
│
└── views/
    ├── header.php          # Ortak üst şablon
    └── footer.php          # Ortak alt şablon
```
 
---
 
## 🛠️ Kullanılan Teknolojiler
 
| Katman | Teknoloji |
|--------|-----------|
| Sunucu Tarafı | PHP |
| Veritabanı | MySQL |
| Arayüz | Bootstrap 5.3 |
 
---
 
## ⚙️ Kurulum
 
1. Projeyi web sunucunuza kopyalayın (Apache/Nginx + PHP)
2. `config/db.php` dosyasını açın ve veritabanı bilgilerinizi girin:
   ```php
   $host = 'localhost';
   $dbname = 'akvaryum_db';
   $username = 'kullanici';
   $password = 'sifre';
   ```
3. Aşağıdaki SQL ile veritabanı tablosunu oluşturun:
```sql
CREATE TABLE akvaryum_takip (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tur_adi VARCHAR(255) NOT NULL,
    bolum_adi VARCHAR(255) NOT NULL,
    tank_numarasi VARCHAR(50) NOT NULL,
    saglik_durumu VARCHAR(100) NOT NULL,
    canli_sayisi INT DEFAULT 1,
    son_yemleme DATETIME,
    son_bakim_notu TEXT,
    sorumlu_personel VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE TABLE kullanicilar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(255) NOT NULL,
    eposta VARCHAR(255) UNIQUE NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    unvan VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
 
4. Tarayıcıdan `register.php` sayfasına giderek ilk hesabınızı oluşturun.
---
 
## 🔒 Güvenlik Özellikleri
 
- Şifreler `password_hash()` ile hashlenerek saklanır
- Tüm veritabanı sorguları PDO Prepared Statements kullanır (SQL injection koruması)
---
 
## 👨‍💻 Yapımcılar
 
| Ad Soyad | Öğrenci No |
|----------|------------|
| **Vera Değerli** | 23360859003 |
| **Esma Öztürk** | 23360859021 |
 
---
 
