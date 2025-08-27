<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$photo_id = $_GET['id'] ?? null;

if (!$photo_id) {
    header('Location: progress_photos.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM progress_photos WHERE id = ? AND user_id = ?");
$stmt->execute([$photo_id, $user_id]);
$photo = $stmt->fetch();

if ($photo) {

    if (file_exists($photo['photo_url'])) {
        unlink($photo['photo_url']);
    }
    $stmt = $pdo->prepare("DELETE FROM progress_photos WHERE id = ? AND user_id = ?");
    $stmt->execute([$photo_id, $user_id]);
}

header('Location: progress_photos.php');
exit();
?>