<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$log_id = $_GET['id'] ?? null;

if (!$log_id) {
    header('Location: nutrition.php');
    exit();
}
$stmt = $pdo->prepare("DELETE FROM food_logs WHERE id = ? AND user_id = ?");
$stmt->execute([$log_id, $user_id]);

header('Location: nutrition.php');
exit();
?>