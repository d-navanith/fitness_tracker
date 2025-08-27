<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$workout_id = $_GET['id'] ?? null;

if (!$workout_id) {
    header('Location: workout_history.php');
    exit();
}

$stmt = $pdo->prepare("DELETE FROM workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$workout_id, $user_id]);

header('Location: workout_history.php');
exit();
?>