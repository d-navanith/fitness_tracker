<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $date = $_POST['date'];
    $notes = sanitizeInput($_POST['notes']);
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedfileExtensions = array('jpg', 'jpeg', 'png');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = 'progress_' . $user_id . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = './uploads/progress/';
            $dest_path = $uploadFileDir . $newFileName;
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $pdo->prepare("
                    INSERT INTO progress_photos (user_id, photo_url, date, notes)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$user_id, $dest_path, $date, $notes]);
                
                header('Location: progress_photos.php');
                exit();
            }
        }
    }
}

$stmt = $pdo->prepare("
    SELECT * FROM progress_photos 
    WHERE user_id = ? 
    ORDER BY date DESC
");
$stmt->execute([$user_id]);
$photos = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Profile Photos</h1>
    <p>Manage and update your personal photos</p>
</div>

<div class="card">
    <h2>Progress Photos</h2>
    
    <h3>Upload New Photo</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="upload_photo" value="1">
        
        <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" accept="image/jpeg, image/jpg, image/png" required>
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="3"></textarea>
        </div>
        
        <button type="submit">Upload Photo</button>
    </form>
</div>

<div class="card">
    <h3>Your Progress Photos</h3>
    
    <?php if (count($photos) > 0): ?>
        <div class="photos-grid">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-card">
                    <img src="<?php echo $photo['photo_url']; ?>" alt="Progress Photo">
                    <p><strong>Date:</strong> <?php echo $photo['date']; ?></p>
                    <p><?php echo $photo['notes']; ?></p>
                    <a href="delete_progress_photo.php?id=<?php echo $photo['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this photo?')" 
                       class="btn btn-danger">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No progress photos uploaded yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>