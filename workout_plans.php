<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_plan'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    
    $stmt = $pdo->prepare("
        INSERT INTO workout_plans (user_id, name, description)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $name, $description]);
    
    $plan_id = $pdo->lastInsertId();
    
    if (isset($_POST['exercises'])) {
        foreach ($_POST['exercises'] as $exercise) {
            $exercise_id = (int)$exercise['id'];
            $sets = (int)$exercise['sets'];
            $reps = (int)$exercise['reps'];
            $rest_time = (int)$exercise['rest_time'];
            
            $stmt = $pdo->prepare("
                INSERT INTO workout_plan_exercises (workout_plan_id, exercise_id, sets, reps, rest_time)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$plan_id, $exercise_id, $sets, $reps, $rest_time]);
        }
    }
    
    header('Location: workout_plans.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM workout_plans WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$workout_plans = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM exercises ORDER BY name");
$stmt->execute();
$exercises = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Workout Plans</h1>
    <p>Design your custom workout routine</p>
</div>

<div class="card">
    <h2>Workout Plans</h2>
    
    <h3>Create New Plan</h3>
    <form method="POST" id="workout-plan-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="create_plan" value="1">
        
        <div class="form-group">
            <label>Plan Name</label>
            <input type="text" name="name" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <label>Exercises</label>
            <div id="exercises-container">
                <div class="exercise-row">
                    <select name="exercises[0][id]" class="exercise-select" required>
                        <option value="">Select an exercise</option>
                        <?php foreach ($exercises as $exercise): ?>
                            <option value="<?php echo $exercise['id']; ?>"><?php echo $exercise['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="exercises[0][sets]" placeholder="Sets" min="1" required>
                    <input type="number" name="exercises[0][reps]" placeholder="Reps" min="1" required>
                    <input type="number" name="exercises[0][rest_time]" placeholder="Rest (sec)" min="0" required>
                    <button type="button" class="remove-exercise">Remove</button>
                </div>
            </div>
            <button type="button" id="add-exercise">Add Exercise</button>
        </div>
        
        <button type="submit">Create Plan</button>
    </form>
</div>

<div class="card">
    <h3>Your Workout Plans</h3>
    
    <?php if (count($workout_plans) > 0): ?>
        <div class="workout-plans-grid">
            <?php foreach ($workout_plans as $plan): ?>
                <div class="workout-plan-card">
                    <h4><?php echo $plan['name']; ?></h4>
                    <p><?php echo $plan['description']; ?></p>
                    <p><small>Created: <?php echo date('M j, Y', strtotime($plan['created_at'])); ?></small></p>
                    <div class="plan-actions">
                        <a href="view_workout_plan.php?id=<?php echo $plan['id']; ?>" class="btn">View</a>
                        <a href="start_workout.php?plan_id=<?php echo $plan['id']; ?>" class="btn btn-primary">Start Workout</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No workout plans created yet.</p>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let exerciseCount = 1;
    
    document.getElementById('add-exercise').addEventListener('click', function() {
        const container = document.getElementById('exercises-container');
        const newRow = document.createElement('div');
        newRow.className = 'exercise-row';
        
        newRow.innerHTML = `
            <select name="exercises[${exerciseCount}][id]" class="exercise-select" required>
                <option value="">Select an exercise</option>
                <?php foreach ($exercises as $exercise): ?>
                    <option value="<?php echo $exercise['id']; ?>"><?php echo $exercise['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="exercises[${exerciseCount}][sets]" placeholder="Sets" min="1" required>
            <input type="number" name="exercises[${exerciseCount}][reps]" placeholder="Reps" min="1" required>
            <input type="number" name="exercises[${exerciseCount}][rest_time]" placeholder="Rest (sec)" min="0" required>
            <button type="button" class="remove-exercise">Remove</button>
        `;
        
        container.appendChild(newRow);
        exerciseCount++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-exercise')) {
            e.target.parentElement.remove();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>