<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM workouts WHERE user_id = ? ORDER BY date DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_workouts = $stmt->fetchAll();
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_workouts,
        SUM(duration) as total_duration,
        SUM(calories) as total_calories
    FROM workouts 
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();
?>

<?php include 'includes/header.php'; ?>

<div class="dashboard-welcome">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>Track your fitness journey and achieve your goals</p>
</div>

<div class="stats-container">
    <div class="stat-card">
        <h3><?php echo $stats['total_workouts']; ?></h3>
        <p>Total Workouts</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['total_duration'] ?? 0; ?></h3>
        <p>Minutes Exercised</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['total_calories'] ?? 0; ?></h3>
        <p>Calories Burned</p>
    </div>
</div>

<div class="card">
    <h2>Recent Workouts</h2>
    <?php if (count($recent_workouts) > 0): ?>
        <table>
            <tr>
                <th>Exercise</th>
                <th>Sets</th>
                <th>Reps</th>
                <th>Duration</th>
                <th>Date</th>
            </tr>
            <?php foreach ($recent_workouts as $workout): ?>
            <tr>
                <td><?php echo $workout['exercise_name']; ?></td>
                <td><?php echo $workout['sets']; ?></td>
                <td><?php echo $workout['reps']; ?></td>
                <td><?php echo $workout['duration']; ?> min</td>
                <td><?php echo $workout['date']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No workouts recorded yet. <a href="workout.php">Log your first workout!</a></p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>