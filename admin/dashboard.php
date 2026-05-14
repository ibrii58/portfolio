<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../css/admin.css" />
</head>
<body class="dashboard-body">
<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
// Session timeout (30 dk)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['login_time'] = time();

require_once '../php/db.php';
$pdo = getDB();

// ─── HANDLE ACTIONS ────────────────────────────────────────
$message_alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_project') {
        $stmt = $pdo->prepare(
            "INSERT INTO projects (title, description, technologies, github_url, live_url, created_at)
             VALUES (:t, :d, :tech, :g, :l, NOW())"
        );
        $stmt->execute([
            ':t'    => trim($_POST['title'] ?? ''),
            ':d'    => trim($_POST['description'] ?? ''),
            ':tech' => trim($_POST['technologies'] ?? ''),
            ':g'    => trim($_POST['github_url'] ?? ''),
            ':l'    => trim($_POST['live_url'] ?? ''),
        ]);
        $message_alert = 'success|Proje başarıyla eklendi!';
    }

    if ($action === 'delete_project' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->execute([':id' => (int)$_POST['id']]);
        $message_alert = 'success|Proje silindi.';
    }

    if ($action === 'delete_contact' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
        $stmt->execute([':id' => (int)$_POST['id']]);
        $message_alert = 'success|Mesaj silindi.';
    }

    if ($action === 'mark_read' && !empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = :id");
        $stmt->execute([':id' => (int)$_POST['id']]);
    }
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
$unread   = $pdo->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

[$alert_type, $alert_msg] = $message_alert ? explode('|', $message_alert, 2) : ['', ''];
?>

<div class="dash-layout">
  <!-- SIDEBAR -->
  <aside class="dash-sidebar">
    <div class="dash-brand"><a href="../index.html">İ<span>Ç</span></a></div>
    <nav class="dash-nav">
      <a href="#projects-section" class="dash-nav-item active" data-tab="projects">
        📁 Projeler <span class="badge"><?= count($projects) ?></span>
      </a>
      <a href="#contacts-section" class="dash-nav-item" data-tab="contacts">
        📨 Mesajlar <?php if ($unread > 0): ?><span class="badge badge-red"><?= $unread ?></span><?php endif; ?>
      </a>
    </nav>
    <form method="POST" action="logout.php" class="logout-form">
      <button type="submit" class="btn-logout">Çıkış Yap</button>
    </form>
  </aside>

  <!-- MAIN -->
  <main class="dash-main">
    <div class="dash-header">
      <h1 class="dash-title">Admin Dashboard</h1>
      <span class="dash-user">👤 <?= htmlspecialchars($_SESSION['admin_user']) ?></span>
    </div>

    <?php if ($alert_msg): ?>
      <div class="alert alert-<?= $alert_type ?>"><?= htmlspecialchars($alert_msg) ?></div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="dash-stats">
      <div class="dash-stat">
        <span class="ds-num"><?= count($projects) ?></span>
        <span class="ds-label">Toplam Proje</span>
      </div>
      <div class="dash-stat">
        <span class="ds-num"><?= count($contacts) ?></span>
        <span class="ds-label">Toplam Mesaj</span>
      </div>
      <div class="dash-stat">
        <span class="ds-num"><?= $unread ?></span>
        <span class="ds-label">Okunmamış</span>
      </div>
    </div>

    <!-- PROJECTS TAB -->
    <section class="dash-section" id="projects-section">
      <div class="dash-section-header">
        <h2>Projeler</h2>
        <button class="btn-add" id="toggleAddForm">+ Yeni Proje</button>
      </div>

      <form method="POST" class="add-form" id="addProjectForm" style="display:none">
        <input type="hidden" name="action" value="add_project" />
        <div class="form-row">
          <div class="form-group">
            <label>Proje Adı *</label>
            <input type="text" name="title" required placeholder="Proje başlığı" />
          </div>
          <div class="form-group">
            <label>Teknolojiler</label>
            <input type="text" name="technologies" placeholder="PHP, MySQL, JS (virgülle ayırın)" />
          </div>
        </div>
        <div class="form-group">
          <label>Açıklama *</label>
          <textarea name="description" rows="3" required placeholder="Proje açıklaması..."></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>GitHub URL</label>
            <input type="url" name="github_url" placeholder="https://github.com/..." />
          </div>
          <div class="form-group">
            <label>Live URL</label>
            <input type="url" name="live_url" placeholder="https://..." />
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn-save">Kaydet</button>
          <button type="button" class="btn-cancel" id="cancelAdd">İptal</button>
        </div>
      </form>

      <div class="table-wrap">
        <table class="dash-table">
          <thead>
            <tr><th>#</th><th>Başlık</th><th>Teknolojiler</th><th>Tarih</th><th>İşlem</th></tr>
          </thead>
          <tbody>
            <?php if (empty($projects)): ?>
              <tr><td colspan="5" class="empty-row">Henüz proje eklenmemiş.</td></tr>
            <?php else: ?>
              <?php foreach ($projects as $i => $p): ?>
                <tr>
                  <td><?= $i+1 ?></td>
                  <td>
                    <strong><?= htmlspecialchars($p['title']) ?></strong>
                    <small><?= htmlspecialchars(mb_substr($p['description'], 0, 60)) ?>…</small>
                  </td>
                  <td><span class="tech-badge"><?= htmlspecialchars($p['technologies'] ?? '—') ?></span></td>
                  <td><?= date('d.m.Y', strtotime($p['created_at'])) ?></td>
                  <td>
                    <form method="POST" onsubmit="return confirm('Projeyi silmek istediğinize emin misiniz?')">
                      <input type="hidden" name="action" value="delete_project" />
                      <input type="hidden" name="id" value="<?= $p['id'] ?>" />
                      <button type="submit" class="btn-delete">Sil</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- CONTACTS TAB -->
    <section class="dash-section" id="contacts-section" style="display:none">
      <div class="dash-section-header">
        <h2>Gelen Mesajlar</h2>
      </div>
      <div class="table-wrap">
        <table class="dash-table">
          <thead>
            <tr><th>#</th><th>Gönderen</th><th>Konu</th><th>Tarih</th><th>Durum</th><th>İşlem</th></tr>
          </thead>
          <tbody>
            <?php if (empty($contacts)): ?>
              <tr><td colspan="6" class="empty-row">Henüz mesaj yok.</td></tr>
            <?php else: ?>
              <?php foreach ($contacts as $i => $c): ?>
                <tr class="<?= $c['is_read'] ? '' : 'row-unread' ?>" id="contact-row-<?= $c['id'] ?>">
                  <td><?= $i+1 ?></td>
                  <td>
                    <strong><?= htmlspecialchars($c['name']) ?></strong>
                    <small><?= htmlspecialchars($c['email']) ?></small>
                  </td>
                  <td>
                    <button class="btn-expand" data-msg="<?= htmlspecialchars($c['message']) ?>" data-id="<?= $c['id'] ?>">
                      <?= htmlspecialchars(mb_substr($c['subject'], 0, 30)) ?>
                    </button>
                  </td>
                  <td><?= date('d.m.Y H:i', strtotime($c['created_at'])) ?></td>
                  <td><?= $c['is_read'] ? '<span class="badge-read">Okundu</span>' : '<span class="badge-unread">Yeni</span>' ?></td>
                  <td>
                    <form method="POST" onsubmit="return confirm('Mesajı silmek istediğinize emin misiniz?')">
                      <input type="hidden" name="action" value="delete_contact" />
                      <input type="hidden" name="id" value="<?= $c['id'] ?>" />
                      <button type="submit" class="btn-delete">Sil</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>

<!-- MESSAGE MODAL -->
<div class="modal-overlay" id="msgModal" style="display:none">
  <div class="modal-box">
    <button class="modal-close" id="closeModal">✕</button>
    <h3 class="modal-title" id="modalTitle">Mesaj</h3>
    <p class="modal-body" id="modalBody"></p>
  </div>
</div>

<script>
// Tab switching
document.querySelectorAll('.dash-nav-item').forEach(item => {
  item.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelectorAll('.dash-nav-item').forEach(i => i.classList.remove('active'));
    this.classList.add('active');
    const tab = this.dataset.tab;
    document.getElementById('projects-section').style.display = tab === 'projects' ? 'block' : 'none';
    document.getElementById('contacts-section').style.display = tab === 'contacts' ? 'block' : 'none';
  });
});

// Add project form toggle
document.getElementById('toggleAddForm').addEventListener('click', () => {
  const f = document.getElementById('addProjectForm');
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
});
document.getElementById('cancelAdd').addEventListener('click', () => {
  document.getElementById('addProjectForm').style.display = 'none';
});

// Message expand modal + mark read
document.querySelectorAll('.btn-expand').forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById('modalBody').textContent = this.dataset.msg;
    document.getElementById('modalTitle').textContent = this.textContent.trim();
    document.getElementById('msgModal').style.display = 'flex';
    // Mark as read via fetch
    const id = this.dataset.id;
    const row = document.getElementById('contact-row-' + id);
    if (row && row.classList.contains('row-unread')) {
      const fd = new FormData();
      fd.append('action', 'mark_read'); fd.append('id', id);
      fetch('dashboard.php', { method:'POST', body: fd });
      row.classList.remove('row-unread');
      const badge = row.querySelector('.badge-unread');
      if (badge) { badge.className = 'badge-read'; badge.textContent = 'Okundu'; }
    }
  });
});
document.getElementById('closeModal').addEventListener('click', () => {
  document.getElementById('msgModal').style.display = 'none';
});
document.getElementById('msgModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
</script>
</body>
</html>
