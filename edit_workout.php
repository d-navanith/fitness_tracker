<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$workout_id = $_GET['id'] ?? null;

if (!$workout_id) {
    header('Location: workout_history.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$workout_id, $user_id]);
$workout = $stmt->fetch();

if (!$workout) {
    header('Location: workout_history.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exercise = $_POST['exercise'];
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];
    $duration = $_POST['duration'];
    $calories = $_POST['calories'];
    $date = $_POST['date'];
    
    $stmt = $pdo->prepare("
        UPDATE workouts 
        SET exercise_name = ?, sets = ?, reps = ?, duration = ?, calories = ?, date = ? 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$exercise, $sets, $reps, $duration, $calories, $date, $workout_id, $user_id]);
    
    header('Location: workout_history.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="card">
    <h2>Edit Workout</h2>
    
    <form method="POST">
        <div class="form-group">
            <label>Exercise Name</label>
            <input type="text" name="exercise" value="<?php echo $workout['exercise_name']; ?>" required>
        </div>
        <div class="form-group">
            <label>Sets</label>
            <input type="number" name="sets" value="<?php echo $workout['sets']; ?>" min="1" required>
        </div>
        <div class="form-group">
            <label>Reps</label>
            <input type="number" name="reps" value="<?php echo $workout['reps']; ?>" min="1" required>
        </div>
        <div class="form-group">
            <label>Duration (minutes)</label>
            <input type="number" name="duration" value="<?php echo $workout['duration']; ?>" min="1" required>
        </div>
        <div class="form-group">
            <label>Calories Burned</label>
            <input type="number" name="calories" value="<?php echo $workout['calories']; ?>" min="0">
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo $workout['date']; ?>" required>
        </div>
        <button type="submit">Update Workout</button>
        <a href="workout_history.php" class="btn-cancel">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>