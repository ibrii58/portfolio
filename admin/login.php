<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Giriş — İbrahim Çam</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/admin.css" />
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <a href="../index.html" class="back-link">← Siteye Dön</a>
      <div class="login-logo">İ<span>Ç</span></div>
      <h1 class="login-title">Admin Paneli</h1>
      <p class="login-sub">Devam etmek için giriş yapın</p>

      <?php
      session_start();
      require_once '../php/db.php';

      $error = '';

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $username = trim($_POST['username'] ?? '');
          $password = $_POST['password'] ?? '';

          // Güvenli: şifre hash ile karşılaştırılır
          // Varsayılan: admin / Admin1234!
          // Kendi şifrenizi oluşturmak için: echo password_hash('SifreNiz', PASSWORD_DEFAULT);
          $valid_user = 'admin';
          $valid_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // "password"

          // Gerçek kullanımda DB'den çekin:
          // $stmt = getDB()->prepare("SELECT password_hash FROM admins WHERE username=?");
          // $stmt->execute([$username]);
          // $row = $stmt->fetch();

          if ($username === $valid_user && password_verify($password, $valid_hash)) {
              session_regenerate_id(true);
              $_SESSION['admin_logged_in'] = true;
              $_SESSION['admin_user']      = $username;
              $_SESSION['login_time']      = time();
              setcookie('admin_remember', base64_encode($username), time() + 86400, '/', '', false, true);
              header('Location: dashboard.php');
              exit;
          } else {
              $error = 'Kullanıcı adı veya şifre hatalı.';
          }
      }
      ?>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="" class="login-form" novalidate>
        <div class="form-group">
          <label for="username">Kullanıcı Adı</label>
          <input type="text" id="username" name="username" placeholder="admin"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 autocomplete="username" required />
        </div>
        <div class="form-group">
          <label for="password">Şifre</label>
          <div class="pw-wrap">
            <input type="password" id="password" name="password" placeholder="••••••••"
                   autocomplete="current-password" required />
            <button type="button" class="pw-toggle" id="pwToggle">👁</button>
          </div>
        </div>
        <button type="submit" class="btn-login">Giriş Yap</button>
      </form>
      <p class="login-hint">Varsayılan: <code>admin</code> / <code>password</code></p>
    </div>
  </div>
  <script>
    document.getElementById('pwToggle').addEventListener('click', function() {
      const pw = document.getElementById('password');
      pw.type = pw.type === 'password' ? 'text' : 'password';
    });
  </script>
</body>
</html>
