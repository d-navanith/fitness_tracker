<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$goal_id = $_GET['id'] ?? null;

if (!$goal_id) {
    header('Location: goals.php');
    exit();
}

$stmt = $pdo->prepare("DELETE FROM goals WHERE id = ? AND user_id = ?");
$stmt->execute([$goal_id, $user_id]);

header('Location: goals.php');
exit();
?>