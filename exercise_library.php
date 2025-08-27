<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exercise'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $instructions = sanitizeInput($_POST['instructions']);
    $muscle_group = sanitizeInput($_POST['muscle_group']);
    $equipment = sanitizeInput($_POST['equipment']);
    $difficulty = $_POST['difficulty'];

    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedfileExtensions = array('jpg', 'jpeg', 'png');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = 'exercise_' . time() . '.' . $fileExtension;
            $uploadFileDir = './uploads/exercises/';
            $dest_path = $uploadFileDir . $newFileName;
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = $dest_path;
            }
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO exercises (name, description, instructions, muscle_group, equipment, difficulty, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $description, $instructions, $muscle_group, $equipment, $difficulty, $image_url]);
    
    header('Location: exercise_library.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM exercises ORDER BY name");
$stmt->execute();
$exercises = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Exercise Library</h1>
    <p>Track your exercise routine</p>
</div>

<div class="card">
    <h2>Exercise Library</h2>
    
    <h3>Add New Exercise</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="add_exercise" value="1">
        
        <div class="form-group">
            <label>Exercise Name</label>
            <input type="text" name="name" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <label>Instructions</label>
            <textarea name="instructions" rows="5"></textarea>
        </div>
        
        <div class="form-group">
            <label>Muscle Group</label>
            <select name="muscle_group">
                <option value="chest">Chest</option>
                <option value="back">Back</option>
                <option value="shoulders">Shoulders</option>
                <option value="arms">Arms</option>
                <option value="legs">Legs</option>
                <option value="core">Core</option>
                <option value="cardio">Cardio</option>
                <option value="full_body">Full Body</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Equipment</label>
            <select name="equipment">
                <option value="none">None</option>
                <option value="dumbbells">Dumbbells</option>
                <option value="barbell">Barbell</option>
                <option value="cable">Cable Machine</option>
                <option value="machine">Machine</option>
                <option value="kettlebell">Kettlebell</option>
                <option value="resistance_band">Resistance Band</option>
                <option value="medicine_ball">Medicine Ball</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Difficulty</label>
            <select name="difficulty">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Exercise Image</label>
            <input type="file" name="image" accept="image/jpeg, image/jpg, image/png">
        </div>
        
        <button type="submit">Add Exercise</button>
    </form>
</div>

<div class="card">
    <h3>Exercise List</h3>
    
    <?php if (count($exercises) > 0): ?>
        <div class="exercise-grid">
            <?php foreach ($exercises as $exercise): ?>
                <div class="exercise-card">
                    <?php if ($exercise['image_url']): ?>
                        <img src="<?php echo $exercise['image_url']; ?>" 
                             alt="<?php echo $exercise['name']; ?>" 
                             class="exercise-img">
                    <?php else: ?>
                        <img src="images/default_exercise.png" 
                             alt="Default Exercise Image" 
                             class="exercise-img">
                    <?php endif; ?>
                    <h4><?php echo $exercise['name']; ?></h4>
                    <p><strong>Muscle Group:</strong> <?php echo ucfirst($exercise['muscle_group']); ?></p>
                    <p><strong>Equipment:</strong> <?php echo ucfirst(str_replace('_', ' ', $exercise['equipment'])); ?></p>
                    <p><strong>Difficulty:</strong> <?php echo ucfirst($exercise['difficulty']); ?></p>
                    <p><?php echo substr($exercise['description'], 0, 100) . '...'; ?></p>
                    <a href="exercise_details.php?id=<?php echo $exercise['id']; ?>" class="btn">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No exercises in the library yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
