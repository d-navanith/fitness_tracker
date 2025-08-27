<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_workouts,
        SUM(duration) as total_duration,
        SUM(calories) as total_calories,
        MAX(date) as last_workout
    FROM workouts 
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_info = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->execute([$email, $user_id]);
    
    header('Location: profile.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Profile</h1>
    <p>View and manage your personal information</p>
</div>

<div class="card">
    <h2>Profile</h2>
    
    <div class="profile-info">
        <div class="profile-section">
            <h3>Account Information</h3>
            <p><strong>Username:</strong> <?php echo $user_info['username']; ?></p>
            <p><strong>Email:</strong> <?php echo $user_info['email']; ?></p>
            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user_info['created_at'])); ?></p>
        </div>
        
        <div class="profile-section">
            <h3>Fitness Statistics</h3>
            <p><strong>Total Workouts:</strong> <?php echo $stats['total_workouts']; ?></p>
            <p><strong>Total Duration:</strong> <?php echo $stats['total_duration'] ?? 0; ?> minutes</p>
            <p><strong>Total Calories:</strong> <?php echo $stats['total_calories'] ?? 0; ?></p>
            <p><strong>Last Workout:</strong> <?php echo $stats['last_workout'] ?? 'Never'; ?></p>
        </div>
    </div>
</div>

<div class="card">
    <h3>Update Email</h3>
    
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $user_info['email']; ?>" required>
        </div>
        <button type="submit">Update Email</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>