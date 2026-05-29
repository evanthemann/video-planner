<?php
require 'db.php';
$db = get_db();

$id = (int) ($_POST['id'] ?? 0);

if ($id) {
    $stmt = $db->prepare("DELETE FROM ideas WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
