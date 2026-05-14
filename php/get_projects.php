<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/db.php';

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare("SELECT * FROM projects ORDER BY created_at DESC");
    $stmt->execute();
    $projects = $stmt->fetchAll();
    echo json_encode($projects, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Projeler yüklenemedi.']);
}
