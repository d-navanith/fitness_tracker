<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

try {
    $pdo->exec("ALTER TABLE goals MODIFY COLUMN status ENUM('active', 'inactive', 'completed', 'failed') DEFAULT 'active'");
} catch (Exception $e) {

}

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_goal'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    if (isset($_POST['description']) && isset($_POST['target']) && isset($_POST['unit']) && 
        isset($_POST['start_date']) && isset($_POST['end_date'])) {
        
        $description = sanitizeInput($_POST['description']);
        $target = (int)$_POST['target'];
        $unit = sanitizeInput($_POST['unit']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $stmt = $pdo->prepare("
            INSERT INTO goals (user_id, goal_description, target_value, unit, start_date, end_date, status)
            VALUES (?, ?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([$user_id, $description, $target, $unit, $start_date, $end_date]);
        
        header('Location: goals.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_goal_status'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $goal_id = (int)$_POST['goal_id'];
    $new_status = $_POST['new_status'];
    
    $stmt = $pdo->prepare("UPDATE goals SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_status, $goal_id, $user_id]);
    
    header('Location: goals.php');
    exit();
}

if (isset($_POST['update_progress'])) {
    $goal_id = (int)$_POST['goal_id'];
    $new_value = (int)$_POST['current_value'];
    
    $stmt = $pdo->prepare("UPDATE goals SET current_value = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_value, $goal_id, $user_id]);

    $stmt = $pdo->prepare("SELECT target_value FROM goals WHERE id = ?");
    $stmt->execute([$goal_id]);
    $goal = $stmt->fetch();
    
    if ($new_value >= $goal['target_value']) {
        $stmt = $pdo->prepare("UPDATE goals SET status = 'completed' WHERE id = ?");
        $stmt->execute([$goal_id]);
    }
    
    header('Location: goals.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY end_date DESC");
$stmt->execute([$user_id]);
$goals = $stmt->fetchAll();

$today = date('Y-m-d');
foreach ($goals as $index => $goal) {
    if ($today > $goal['end_date'] && $goal['status'] === 'active') {
        $stmt = $pdo->prepare("UPDATE goals SET status = 'overdue' WHERE id = ?");
        $stmt->execute([$goal['id']]);

        $goals[$index]['status'] = 'overdue';
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Fitness Goals</h1>
    <p>Set and track your fitness objectives</p>
</div>

<div class="card">
    <h2>Create New Goal</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="create_goal" value="1">
        
        <div class="form-group">
            <label>Goal Description</label>
            <input type="text" name="description" required placeholder="e.g., Lose 5kg">
        </div>
        
        <div class="form-group">
            <label>Target Value</label>
            <input type="number" name="target" min="1" required placeholder="e.g., 5">
        </div>
        
        <div class="form-group">
            <label>Unit</label>
            <select name="unit" required>
                <option value="kg">Kilograms</option>
                <option value="miles">Miles</option>
                <option value="reps">Repetitions</option>
                <option value="days">Days</option>
                <option value="workouts">Workouts</option>
                <option value="minutes">Minutes</option>
            </select>
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
        
        <button type="submit" class="btn btn-primary">Create Goal</button>
    </form>
</div>

<div class="card">
    <h2>Your Goals</h2>
    
    <?php if (count($goals) > 0): ?>
        <div class="goals-container">
            <?php foreach ($goals as $goal): ?>
                <?php
                $progress = ($goal['current_value'] / $goal['target_value']) * 100;
                $status = $goal['status'];
                if ($today > $goal['end_date'] && $status === 'active') {
                    $status = 'overdue';
                }
                ?>
                <div class="goal-card">
                    <div class="goal-header">
                        <h3><?php echo htmlspecialchars($goal['goal_description']); ?></h3>
                        
                        <?php if ($status === 'active'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="toggle_goal_status" value="1">
                                <input type="hidden" name="goal_id" value="<?php echo $goal['id']; ?>">
                                <input type="hidden" name="new_status" value="inactive">
                                <button type="submit" class="status-btn active">Active</button>
                            </form>
                        <?php elseif ($status === 'inactive'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="toggle_goal_status" value="1">
                                <input type="hidden" name="goal_id" value="<?php echo $goal['id']; ?>">
                                <input type="hidden" name="new_status" value="active">
                                <button type="submit" class="status-btn inactive">Inactive</button>
                            </form>
                        <?php else: ?>
                            <span class="status-badge <?php echo $status; ?>"><?php echo ucfirst($status); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="goal-progress">
                        <div class="progress-info">
                            <span class="progress-text"><?php echo $goal['current_value']; ?> / <?php echo $goal['target_value']; ?> <?php echo $goal['unit']; ?></span>
                            <span class="progress-percent"><?php echo round(min($progress, 100)); ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo min($progress, 100); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="goal-meta">
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>End Date: <?php echo $goal['end_date']; ?></span>
                        </div>
                        
                        <div class="goal-actions">
                            <?php if ($status === 'active'): ?>
                                <div class="goal-update">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="update_progress" value="1">
                                        <input type="hidden" name="goal_id" value="<?php echo $goal['id']; ?>">
                                        
                                        <div class="update-form">
                                            <input type="number" name="current_value" 
                                                   value="<?php echo $goal['current_value']; ?>" 
                                                   min="0" max="<?php echo $goal['target_value']; ?>"
                                                   placeholder="Current value">
                                            <button type="submit">Update</button>
                                        </div>
                                    </form>
                                </div>
                            <?php elseif ($status === 'completed' || $status === 'overdue'): ?>
                                <div class="goal-activate">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="toggle_goal_status" value="1">
                                        <input type="hidden" name="goal_id" value="<?php echo $goal['id']; ?>">
                                        <input type="hidden" name="new_status" value="active">
                                        <button type="submit" class="btn btn-success">Activate Goal</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"></path>
                <path d="M12 22V12"></path>
                <path d="M12 12L8 8"></path>
                <path d="M12 12L16 8"></path>
            </svg>
            <h3>No Goals Set</h3>
            <p>You haven't set any fitness goals yet. Create your first goal to get started!</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>