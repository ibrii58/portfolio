# 🚀 Full-Stack Portfolio — İbrahim Çam

Kişisel portfolyo web uygulaması. HTML5, CSS3, JavaScript, PHP ve MySQL ile geliştirilmiştir.

🌐 **Live Demo:** [ibrahimcam.infinityfreeapp.com](http://ibrahimcam.infinityfreeapp.com)  
💻 **GitHub:** [github.com/cibrahim58](https://github.com/cibrahim58)

---

## Özellikler
- Responsive tasarım (mobile-first)
- Dark / Light mode (Cookie + localStorage)
- AJAX ile proje yükleme (Fetch API)
- İletişim formu (JS validasyon + PHP backend + MySQL)
- Admin Dashboard (Session yönetimi, proje ekle/sil, mesaj oku)
- Custom cursor, scroll animasyonları, skill bar animasyonu

## Kurulum (Lokal)

```bash
# 1. Dosyaları XAMPP/WAMP htdocs klasörüne kopyalayın
# 2. portfolio_db.sql dosyasını phpMyAdmin'den import edin
# 3. php/db.php içindeki bilgileri güncelleyin:
```

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');
```

## Admin Paneli
- **URL:** `/admin/login.php`
- **Kullanıcı:** `admin`
- **Şifre:** `password`

> ⚠️ Production'da mutlaka şifre değiştirin!

## Dosya Yapısı
```
portfolio/
├── index.html
├── portfolio_db.sql
├── css/
│   ├── style.css
│   └── admin.css
├── js/
│   └── main.js
├── php/
│   ├── db.php
│   ├── get_projects.php
│   └── submit_contact.php
└── admin/
    ├── login.php
    ├── dashboard.php
    └── logout.php
```

## Teknolojiler
HTML5 • CSS3 • JavaScript (ES6+) • PHP 8 • MySQL • PDO • Fetch API