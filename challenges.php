<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_challenge'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $type = $_POST['type'];
    $target_value = (int)$_POST['target_value'];
    $unit = sanitizeInput($_POST['unit']);
    
    $stmt = $pdo->prepare("
        INSERT INTO challenges (name, description, start_date, end_date, type, target_value, unit, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $description, $start_date, $end_date, $type, $target_value, $unit, $user_id]);
    
    $challenge_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("
        INSERT INTO challenge_participants (challenge_id, user_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$challenge_id, $user_id]);
    
    header('Location: challenges.php');
    exit();
}

if (isset($_GET['join'])) {
    $challenge_id = (int)$_GET['join'];

    $stmt = $pdo->prepare("
        SELECT id FROM challenge_participants 
        WHERE challenge_id = ? AND user_id = ?
    ");
    $stmt->execute([$challenge_id, $user_id]);
    $existing = $stmt->fetch();
    
    if (!$existing) {
        $stmt = $pdo->prepare("
            INSERT INTO challenge_participants (challenge_id, user_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$challenge_id, $user_id]);
    }
    
    header('Location: challenges.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT c.*, u.username 
    FROM challenges c 
    JOIN users u ON c.created_by = u.id 
    ORDER BY c.start_date DESC
");
$stmt->execute();
$challenges = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT cp.*, c.name, c.description, c.start_date, c.end_date, c.type, c.target_value, c.unit
    FROM challenge_participants cp
    JOIN challenges c ON cp.challenge_id = c.id
    WHERE cp.user_id = ?
    ORDER BY cp.joined_at DESC
");
$stmt->execute([$user_id]);
$user_challenges = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Fitness Challenges</h1>
    <p>Join challenges and compete with others</p>
</div>

<div class="card">
    <h2>Create New Challenge</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="create_challenge" value="1">
        
        <div class="form-group">
            <label>Challenge Name</label>
            <input type="text" name="name" required placeholder="e.g., 30-Day Plank Challenge">
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Describe your challenge..."></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" required>
            </div>
            
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Challenge Type</label>
                <select name="type" required>
                    <option value="workout_count">Workout Count</option>
                    <option value="calories_burned">Calories Burned</option>
                    <option value="steps_count">Steps Count</option>
                    <option value="custom">Custom</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Target Value</label>
                <input type="number" name="target_value" min="1" required placeholder="e.g., 30">
            </div>
        </div>
        
        <div class="form-group">
            <label>Unit</label>
            <input type="text" name="unit" required placeholder="e.g., workouts, calories, steps">
        </div>
        
        <button type="submit" class="btn btn-primary">Create Challenge</button>
    </form>
</div>

<div class="card">
    <h2>Available Challenges</h2>
    
    <?php if (count($challenges) > 0): ?>
        <div class="challenges-grid">
            <?php foreach ($challenges as $challenge): ?>
                <div class="challenge-card">
                    <h3><?php echo htmlspecialchars($challenge['name']); ?></h3>
                    <p><?php echo htmlspecialchars($challenge['description']); ?></p>
                    <div class="challenge-details">
                        <p><strong>Type:</strong> <?php echo ucfirst(str_replace('_', ' ', $challenge['type'])); ?></p>
                        <p><strong>Target:</strong> <?php echo $challenge['target_value']; ?> <?php echo $challenge['unit']; ?></p>
                        <p><strong>Dates:</strong> <?php echo $challenge['start_date']; ?> to <?php echo $challenge['end_date']; ?></p>
                        <p><small>Created by <?php echo $challenge['username']; ?></small></p>
                    </div>
                    
                    <?php
                    $is_participating = false;
                    foreach ($user_challenges as $uc) {
                        if ($uc['challenge_id'] == $challenge['id']) {
                            $is_participating = true;
                            break;
                        }
                    }
                    ?>
                    
                    <?php if ($is_participating): ?>
                        <span class="badge">Joined</span>
                    <?php else: ?>
                        <a href="challenges.php?join=<?php echo $challenge['id']; ?>" class="btn btn-primary">Join Challenge</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <h3>No Challenges Available</h3>
            <p>There are no challenges available right now. Create your own challenge!</p>
            <a href="#create-challenge" class="btn btn-primary">Create Challenge</a>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Your Challenges</h2>
    
    <?php if (count($user_challenges) > 0): ?>
        <div class="user-challenges">
            <?php foreach ($user_challenges as $challenge): ?>
                <?php
                $progress = ($challenge['current_value'] / $challenge['target_value']) * 100;
                $status = 'Active';
                
                if (date('Y-m-d') > $challenge['end_date']) {
                    $status = 'Completed';
                }
                ?>
                <div class="user-challenge">
                    <div class="challenge-header">
                        <h3><?php echo htmlspecialchars($challenge['name']); ?></h3>
                        <span class="status-badge <?php echo strtolower($status); ?>"><?php echo $status; ?></span>
                    </div>
                    
                    <div class="progress-section">
                        <div class="progress-info">
                            <span class="progress-text"><?php echo $challenge['current_value']; ?> / <?php echo $challenge['target_value']; ?> <?php echo $challenge['unit']; ?></span>
                            <span class="progress-percent"><?php echo round(min($progress, 100)); ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo min($progress, 100); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="challenge-meta">
                        <p><strong>Target:</strong> <?php echo $challenge['target_value']; ?> <?php echo $challenge['unit']; ?></p>
                        <p><strong>End Date:</strong> <?php echo $challenge['end_date']; ?></p>
                        <p><strong>Type:</strong> <?php echo ucfirst(str_replace('_', ' ', $challenge['type'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 12 9"></polyline>
            </svg>
            <h3>No Challenges Joined</h3>
            <p>You haven't joined any challenges yet. Join one from the available challenges!</p>
            <a href="#available-challenges" class="btn btn-primary">Browse Challenges</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>