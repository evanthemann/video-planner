<?php
require 'db.php';
header('Content-Type: application/json');

$id         = (int) ($_POST['id'] ?? 0);
$title      = trim($_POST['title'] ?? '');
$brain_dump = trim($_POST['brain_dump'] ?? '');

if (!$id) {
    echo json_encode(['ok' => false]);
    exit;
}

$db = get_db();
$stmt = $db->prepare("UPDATE ideas SET title=?, brain_dump=?, updated_at=datetime('now') WHERE id=?");
$stmt->execute([$title, $brain_dump, $id]);

echo json_encode(['ok' => true]);
