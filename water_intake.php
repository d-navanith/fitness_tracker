<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();
$today = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_water'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $amount = (int)$_POST['amount'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    
    $stmt = $pdo->prepare("
        INSERT INTO water_intake (user_id, amount, date, time)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $amount, $date, $time]);
    
    header('Location: water_intake.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT SUM(amount) as total_amount 
    FROM water_intake 
    WHERE user_id = ? AND date = ?
");
$stmt->execute([$user_id, $today]);
$today_intake = $stmt->fetch();
$total_today = $today_intake['total_amount'] ?? 0;

$stmt = $pdo->prepare("
    SELECT date, SUM(amount) as total_amount 
    FROM water_intake 
    WHERE user_id = ? 
    GROUP BY date 
    ORDER BY date DESC 
    LIMIT 7
");
$stmt->execute([$user_id]);
$intake_history = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT * FROM water_intake 
    WHERE user_id = ? AND date = ? 
    ORDER BY time DESC
");
$stmt->execute([$user_id, $today]);
$today_entries = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Water Intake</h1>
    <p>Log and monitor your daily hydration</p>
</div>

<div class="card">
    <h2>Water Intake Tracker</h2>
    
    <div class="water-summary">
        <div class="water-goal">
            <h3>Today's Intake</h3>
            <div class="water-amount"><?php echo $total_today; ?> ml</div>
            <div class="water-progress">
                <div class="progress-bar">
                    <div class="progress" style="width: <?php echo min(($total_today / 2000) * 100, 100); ?>%"></div>
                </div>
                <small>Goal: 2000 ml</small>
            </div>
        </div>
        
        <div class="quick-add">
            <h3>Quick Add</h3>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="log_water" value="1">
                <input type="hidden" name="date" value="<?php echo $today; ?>">
                <input type="hidden" name="time" value="<?php echo date('H:i'); ?>">
                
                <div class="quick-add-buttons">
                    <button type="submit" name="amount" value="250">250 ml</button>
                    <button type="submit" name="amount" value="500">500 ml</button>
                    <button type="submit" name="amount" value="750">750 ml</button>
                    <button type="submit" name="amount" value="1000">1000 ml</button>
                </div>
            </form>
        </div>
    </div>
    
    <h3>Log Water Intake</h3>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="log_water" value="1">
        
        <div class="form-group">
            <label>Amount (ml)</label>
            <input type="number" name="amount" min="1" required>
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo $today; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Time</label>
            <input type="time" name="time" value="<?php echo date('H:i'); ?>" required>
        </div>
        
        <button type="submit">Log Water</button>
    </form>
</div>

<div class="card">
    <h3>Today's Water Entries</h3>
    
    <?php if (count($today_entries) > 0): ?>
        <table>
            <tr>
                <th>Time</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($today_entries as $entry): ?>
            <tr>
                <td><?php echo $entry['time']; ?></td>
                <td><?php echo $entry['amount']; ?> ml</td>
                <td>
                    <a href="delete_water_entry.php?id=<?php echo $entry['id']; ?>" 
                       onclick="return confirm('Are you sure?')" 
                       class="btn btn-danger">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No water intake logged today.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Water Intake History (Last 7 Days)</h3>
    
    <?php if (count($intake_history) > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Total Intake</th>
                <th>Goal Progress</th>
            </tr>
            <?php foreach ($intake_history as $day): ?>
                <?php $progress = ($day['total_amount'] / 2000) * 100; ?>
            <tr>
                <td><?php echo $day['date']; ?></td>
                <td><?php echo $day['total_amount']; ?> ml</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo min($progress, 100); ?>%"></div>
                    </div>
                    <small><?php echo round($progress, 1); ?>%</small>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No water intake history yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>