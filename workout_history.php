<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$filter_exercise = $_GET['exercise'] ?? '';
$filter_date = $_GET['date'] ?? '';

$query = "SELECT * FROM workouts WHERE user_id = ?";
$params = [$user_id];

if (!empty($filter_exercise)) {
    $query .= " AND exercise_name LIKE ?";
    $params[] = "%$filter_exercise%";
}

if (!empty($filter_date)) {
    $query .= " AND date = ?";
    $params[] = $filter_date;
}

$query .= " ORDER BY date DESC, id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$workouts = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT DISTINCT exercise_name FROM workouts WHERE user_id = ? ORDER BY exercise_name");
$stmt->execute([$user_id]);
$exercises = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Workout History</h1>
    <p>Keep a record of your exercise sessions</p>
</div>


<div class="card">
    <h2>Workout History</h2>

    <form method="GET" class="filter-form">
        <div class="form-group">
            <label>Exercise</label>
            <select name="exercise">
                <option value="">All Exercises</option>
                <?php foreach ($exercises as $exercise): ?>
                    <option value="<?php echo $exercise['exercise_name']; ?>" 
                        <?php echo ($filter_exercise == $exercise['exercise_name']) ? 'selected' : ''; ?>>
                        <?php echo $exercise['exercise_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo $filter_date; ?>">
        </div>
        
        <button type="submit">Filter</button>
        <a href="workout_history.php" class="btn-reset">Reset</a>
    </form>
    
    <?php if (count($workouts) > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Exercise</th>
                <th>Sets</th>
                <th>Reps</th>
                <th>Duration</th>
                <th>Calories</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($workouts as $workout): ?>
            <tr>
                <td><?php echo $workout['date']; ?></td>
                <td><?php echo $workout['exercise_name']; ?></td>
                <td><?php echo $workout['sets']; ?></td>
                <td><?php echo $workout['reps']; ?></td>
                <td><?php echo $workout['duration']; ?> min</td>
                <td><?php echo $workout['calories'] ?: 'N/A'; ?></td>
                <td>
                    <a href="edit_workout.php?id=<?php echo $workout['id']; ?>">Edit</a> |
                    <a href="delete_workout.php?id=<?php echo $workout['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this workout?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No workouts found matching your criteria.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>