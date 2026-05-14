<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Yöntem desteklenmiyor.']);
    exit;
}

require_once __DIR__ . '/db.php';

// ─── INPUT SANITIZATION ───────────────────────────────────
function clean(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

$name    = clean($_POST['name']    ?? '');
$email   = clean($_POST['email']   ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = clean($_POST['message'] ?? '');

// ─── SERVER-SIDE VALIDATION ───────────────────────────────
$errors = [];
if (strlen($name) < 2)                     $errors[] = 'Ad en az 2 karakter olmalı.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli bir e-posta girin.';
if (strlen($subject) < 3)                  $errors[] = 'Konu en az 3 karakter olmalı.';
if (strlen($message) < 10)                 $errors[] = 'Mesaj en az 10 karakter olmalı.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ─── SAVE TO DATABASE ─────────────────────────────────────
try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        "INSERT INTO contacts (name, email, subject, message, ip_address, created_at)
         VALUES (:name, :email, :subject, :message, :ip, NOW())"
    );
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message,
        ':ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);

    echo json_encode(['success' => true, 'message' => 'Mesajınız alındı!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Mesaj kaydedilemedi.']);
}
