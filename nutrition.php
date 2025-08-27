<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();
$today = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_food'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $food_id = (int)$_POST['food_id'];
    $servings = (float)$_POST['servings'];
    $meal_type = $_POST['meal_type'];
    $log_date = $_POST['log_date'];
    
    $stmt = $pdo->prepare("
        INSERT INTO food_logs (user_id, food_id, servings, meal_type, log_date)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $food_id, $servings, $meal_type, $log_date]);
    
    header('Location: nutrition.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT fl.id, fl.servings, fl.meal_type, fi.name, fi.calories_per_serving, fi.protein, fi.carbs, fi.fat
    FROM food_logs fl
    JOIN food_items fi ON fl.food_id = fi.id
    WHERE fl.user_id = ? AND fl.log_date = ?
    ORDER BY fl.meal_type
");
$stmt->execute([$user_id, $today]);
$food_logs = $stmt->fetchAll();

$total_calories = 0;
$total_protein = 0;
$total_carbs = 0;
$total_fat = 0;

foreach ($food_logs as $log) {
    $total_calories += $log['calories_per_serving'] * $log['servings'];
    $total_protein += $log['protein'] * $log['servings'];
    $total_carbs += $log['carbs'] * $log['servings'];
    $total_fat += $log['fat'] * $log['servings'];
}

$stmt = $pdo->prepare("SELECT * FROM food_items ORDER BY name");
$stmt->execute();
$food_items = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Nutrition Tracker</h1>
    <p>Track your daily food intake and nutrition</p>
</div>

<div class="nutrition-summary">
    <div class="summary-card">
        <div class="summary-icon calories">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
        </div>
        <div class="summary-content">
            <h3>Calories</h3>
            <div class="summary-value"><?php echo round($total_calories); ?></div>
            <div class="summary-goal">Goal: 2000 kcal</div>
        </div>
    </div>
    
    <div class="summary-card">
        <div class="summary-icon protein">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"></path>
            </svg>
        </div>
        <div class="summary-content">
            <h3>Protein</h3>
            <div class="summary-value"><?php echo round($total_protein, 1); ?>g</div>
            <div class="summary-goal">Goal: 150g</div>
        </div>
    </div>
    
    <div class="summary-card">
        <div class="summary-icon carbs">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 6v6l4 2"></path>
            </svg>
        </div>
        <div class="summary-content">
            <h3>Carbs</h3>
            <div class="summary-value"><?php echo round($total_carbs, 1); ?>g</div>
            <div class="summary-goal">Goal: 250g</div>
        </div>
    </div>
    
    <div class="summary-card">
        <div class="summary-icon fat">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
        </div>
        <div class="summary-content">
            <h3>Fat</h3>
            <div class="summary-value"><?php echo round($total_fat, 1); ?>g</div>
            <div class="summary-goal">Goal: 65g</div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Log Food</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="log_food" value="1">
        
        <div class="form-row">
            <div class="form-group">
                <label>Food Item</label>
                <select name="food_id" required>
                    <option value="">Select a food</option>
                    <?php foreach ($food_items as $food): ?>
                        <option value="<?php echo $food['id']; ?>">
                            <?php echo htmlspecialchars($food['name']); ?> (<?php echo $food['calories_per_serving']; ?> kcal/<?php echo $food['serving_size']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Servings</label>
                <input type="number" name="servings" step="0.1" min="0.1" required placeholder="e.g., 1.5">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Meal Type</label>
                <select name="meal_type" required>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                    <option value="snack">Snack</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="log_date" value="<?php echo $today; ?>" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Log Food</button>
    </form>
</div>

<div class="card">
    <h2>Today's Food Log</h2>
    
    <?php if (count($food_logs) > 0): ?>
        <div class="food-log-container">
            <?php 
            $meals = ['breakfast', 'lunch', 'dinner', 'snack'];
            foreach ($meals as $meal): 
                $meal_logs = array_filter($food_logs, function($log) use ($meal) {
                    return $log['meal_type'] === $meal;
                });
                
                if (count($meal_logs) > 0):
            ?>
                <div class="meal-section">
                    <h3><?php echo ucfirst($meal); ?></h3>
                    <div class="meal-items">
                        <?php foreach ($meal_logs as $log): ?>
                            <div class="food-item">
                                <div class="food-info">
                                    <h4><?php echo htmlspecialchars($log['name']); ?></h4>
                                    <div class="food-details">
                                        <span><?php echo $log['servings']; ?> serving(s)</span>
                                        <span><?php echo round($log['calories_per_serving'] * $log['servings']); ?> kcal</span>
                                    </div>
                                </div>
                                <div class="food-macros">
                                    <div class="macro-item">
                                        <span class="macro-label">P</span>
                                        <span class="macro-value"><?php echo round($log['protein'] * $log['servings'], 1); ?>g</span>
                                    </div>
                                    <div class="macro-item">
                                        <span class="macro-label">C</span>
                                        <span class="macro-value"><?php echo round($log['carbs'] * $log['servings'], 1); ?>g</span>
                                    </div>
                                    <div class="macro-item">
                                        <span class="macro-label">F</span>
                                        <span class="macro-value"><?php echo round($log['fat'] * $log['servings'], 1); ?>g</span>
                                    </div>
                                </div>
                                <div class="food-actions">
                                    <a href="delete_food_log.php?id=<?php echo $log['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this food log?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 0 1-3 0 2.704 2.704 0 0 1-3 0 2.704 2.704 0 0 1-3 0 2.704 2.704 0 0 1-3 0 2.704 2.704 0 0 1-3 0 2.704 2.704 0 0 1-3 0 2.704 2.704 0 0 1-3 0c-.454-.303-.977-.454-1.5-.454"></path>
            <path d="M12 3v18"></path>
            <path d="M3 12h18"></path>
            <circle cx="12" cy="12" r="3"></circle>
            <circle cx="12" cy="12" r="7"></circle>
            <circle cx="12" cy="12" r="11"></circle>
            <path d="M20.49 3.51a10 10 0 0 0-17 17"></path>
                <path d="M3.51 3.51a10 10 0 0 0 17 17"></path>
            </svg>
            <h3>No Food Logged Today</h3>
            <p>Start tracking your nutrition by logging your meals</p>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Nutrition Chart</h2>
    <div class="chart-container">
        <canvas id="nutritionChart"></canvas>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const ctx = document.getElementById('nutritionChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Protein', 'Carbs', 'Fat'],
            datasets: [{
                data: [
                    <?php echo round($total_protein * 4); ?>,
                    <?php echo round($total_carbs * 4); ?>,
                    <?php echo round($total_fat * 9); ?>
                ],
                backgroundColor: [
                    '#3498db',
                    '#2ecc71',
                    '#f39c12'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100);
                            return label + ': ' + percentage + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>