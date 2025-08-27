<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$entry_id = $_GET['id'] ?? null;

if (!$entry_id) {
    header('Location: water_intake.php');
    exit();
}

$stmt = $pdo->prepare("DELETE FROM water_intake WHERE id = ? AND user_id = ?");
$stmt->execute([$entry_id, $user_id]);

header('Location: water_intake.php');
exit();
?>