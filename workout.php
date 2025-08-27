<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $exercise = sanitizeInput($_POST['exercise']);
    $sets = (int)$_POST['sets'];
    $reps = (int)$_POST['reps'];
    $duration = (int)$_POST['duration'];
    $calories = (int)$_POST['calories'];
    $date = $_POST['date'];
    
    $stmt = $pdo->prepare("INSERT INTO workouts (user_id, exercise_name, sets, reps, duration, calories, date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $exercise, $sets, $reps, $duration, $calories, $date]);
    
    header('Location: dashboard.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Log Workout</h1>
    <p>Log your exercises and monitor progress</p>
</div>

<div class="card">
    <h2>Log New Workout</h2>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="form-group">
            <label>Exercise Name</label>
            <input type="text" name="exercise" required>
        </div>
        
        <div class="form-group">
            <label>Sets</label>
            <input type="number" name="sets" min="1" required>
        </div>
        
        <div class="form-group">
            <label>Reps</label>
            <input type="number" name="reps" min="1" required>
        </div>
        
        <div class="form-group">
            <label>Duration (minutes)</label>
            <input type="number" name="duration" min="1" required>
        </div>
        
        <div class="form-group">
            <label>Calories Burned</label>
            <input type="number" name="calories" min="0">
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <button type="submit">Log Workout</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>