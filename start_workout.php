<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$plan_id = $_GET['plan_id'] ?? null;
$csrf_token = generateCSRFToken();

if (!$plan_id) {
    header('Location: workout_plans.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT wp.* 
    FROM workout_plans wp 
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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_workout'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $workout_date = $_POST['workout_date'];
    $duration = (int)$_POST['duration'];

    foreach ($_POST['exercises'] as $exercise_id => $data) {
        $sets = (int)$data['sets_completed'];
        $reps = (int)$data['reps_completed'];

        $stmt = $pdo->prepare("SELECT name FROM exercises WHERE id = ?");
        $stmt->execute([$exercise_id]);
        $exercise = $stmt->fetch();
        
        $stmt = $pdo->prepare("
            INSERT INTO workouts (user_id, exercise_name, sets, reps, duration, date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $exercise['name'], $sets, $reps, $duration / count($plan_exercises), $workout_date]);
    }
    
    header('Location: dashboard.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="card">
    <h2>Start Workout: <?php echo $plan['name']; ?></h2>
    
    <form method="POST" id="workout-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="complete_workout" value="1">
        
        <div class="form-group">
            <label>Workout Date</label>
            <input type="date" name="workout_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Total Duration (minutes)</label>
            <input type="number" name="duration" min="1" required>
        </div>
        
        <h3>Exercises</h3>
        <?php if (count($plan_exercises) > 0): ?>
            <table>
                <tr>
                    <th>Exercise</th>
                    <th>Sets</th>
                    <th>Reps</th>
                    <th>Sets Completed</th>
                    <th>Reps Completed</th>
                </tr>
                <?php foreach ($plan_exercises as $exercise): ?>
                <tr>
                    <td><?php echo $exercise['name']; ?></td>
                    <td><?php echo $exercise['sets']; ?></td>
                    <td><?php echo $exercise['reps']; ?></td>
                    <td>
                        <input type="number" name="exercises[<?php echo $exercise['exercise_id']; ?>][sets_completed]" 
                               min="0" max="<?php echo $exercise['sets']; ?>" value="<?php echo $exercise['sets']; ?>">
                    </td>
                    <td>
                        <input type="number" name="exercises[<?php echo $exercise['exercise_id']; ?>][reps_completed]" 
                               min="0" max="<?php echo $exercise['reps']; ?>" value="<?php echo $exercise['reps']; ?>">
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No exercises in this plan.</p>
        <?php endif; ?>
        
        <button type="submit">Complete Workout</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>