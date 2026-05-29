<?php
require 'db.php';
$db = get_db();

$brain_dump = trim($_POST['brain_dump'] ?? '');

if ($brain_dump !== '') {
    $stmt = $db->prepare("INSERT INTO ideas (brain_dump) VALUES (?)");
    $stmt->execute([$brain_dump]);
}

header('Location: index.php');
exit;
