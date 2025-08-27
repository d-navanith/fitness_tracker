<?php
require_once 'config/session.php';
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$plan_id = $_GET['id'] ?? null;

if (!$plan_id) {
    header('Location: workout_plans.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT wp.*, u.username 
    FROM workout_plans wp 
    JOIN users u ON wp.user_id = u.id 
    WHERE wp.id = ? AND wp.user_id = ?
");
$stmt->execute([$plan_id, $user_id]);
$plan = $stmt->fetch();

if (!$plan) {
    header('Location: workout_plans.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT wpe.*, e.name, e.muscle_group, e.equipment 
    FROM workout_plan_exercises wpe 
    JOIN exercises e ON wpe.exercise_id = e.id 
    WHERE wpe.workout_plan_id = ?
    ORDER BY wpe.id
");
$stmt->execute([$plan_id]);
$plan_exercises = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="card">
    <div class="workout-plan-header">
        <h2><?php echo $plan['name']; ?></h2>
        <p><?php echo $plan['description']; ?></p>
        <p><small>Created by <?php echo $plan['username']; ?> on <?php echo date('M j, Y', strtotime($plan['created_at'])); ?></small></p>
        <div class="plan-actions">
            <a href="workout_plans.php" class="btn">Back to Plans</a>
            <a href="start_workout.php?plan_id=<?php echo $plan['id']; ?>" class="btn btn-primary">Start Workout</a>
        </div>
    </div>
    
    <h3>Exercises</h3>
    <?php if (count($plan_exercises) > 0): ?>
        <table>
            <tr>
                <th>Exercise</th>
                <th>Muscle Group</th>
                <th>Equipment</th>
                <th>Sets</th>
                <th>Reps</th>
                <th>Rest Time</th>
            </tr>
            <?php foreach ($plan_exercises as $exercise): ?>
            <tr>
                <td><?php echo $exercise['name']; ?></td>
                <td><?php echo ucfirst($exercise['muscle_group']); ?></td>
                <td><?php echo ucfirst(str_replace('_', ' ', $exercise['equipment'])); ?></td>
                <td><?php echo $exercise['sets']; ?></td>
                <td><?php echo $exercise['reps']; ?></td>
                <td><?php echo $exercise['rest_time']; ?> sec</td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No exercises in this plan.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>