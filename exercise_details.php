<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

if (!isset($_GET['id'])) {
    header("Location: exercise_library.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM exercises WHERE id = ?");
$stmt->execute([$id]);
$exercise = $stmt->fetch();

if (!$exercise) {
    echo "Exercise not found.";
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1><?php echo htmlspecialchars($exercise['name']); ?></h1>
    <p>Exercise Details</p>
</div>

<div class="card">
    <div class="exercise-details">
        <?php if ($exercise['image_url']): ?>
            <img src="<?php echo $exercise['image_url']; ?>" 
                 alt="<?php echo htmlspecialchars($exercise['name']); ?>" 
                 class="exercise-img">
        <?php else: ?>
            <img src="images/default_exercise.png" 
                 alt="Default Exercise Image" 
                 class="exercise-img">
        <?php endif; ?>

        <h2><?php echo htmlspecialchars($exercise['name']); ?></h2>
        <p><strong>Muscle Group:</strong> <?php echo ucfirst($exercise['muscle_group']); ?></p>
        <p><strong>Equipment:</strong> <?php echo ucfirst(str_replace('_', ' ', $exercise['equipment'])); ?></p>
        <p><strong>Difficulty:</strong> <?php echo ucfirst($exercise['difficulty']); ?></p>

        <div class="exercise-description">
            <strong>Description:</strong>
            <p><?php echo nl2br(htmlspecialchars($exercise['description'])); ?></p>
        </div>

        <div class="exercise-instructions">
            <strong>Instructions:</strong>
            <p><?php echo nl2br(htmlspecialchars($exercise['instructions'])); ?></p>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
