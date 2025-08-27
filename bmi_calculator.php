<?php
require_once 'config/session.php';
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT * FROM bmi_records 
    WHERE user_id = ? 
    ORDER BY date DESC 
    LIMIT 1
");
$stmt->execute([$user_id]);
$latest_bmi = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = (float)$_POST['weight'];
    $height = (float)$_POST['height'];
    $date = $_POST['date'];
    $bmi = $weight / (($height / 100) * ($height / 100));

    if ($bmi < 18.5) {
        $category = 'Underweight';
    } elseif ($bmi < 25) {
        $category = 'Normal weight';
    } elseif ($bmi < 30) {
        $category = 'Overweight';
    } else {
        $category = 'Obese';
    }

    $stmt = $pdo->prepare("
        INSERT INTO bmi_records (user_id, weight, height, bmi, category, date)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $weight, $height, $bmi, $category, $date]);
    
    header('Location: bmi_calculator.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT * FROM bmi_records 
    WHERE user_id = ? 
    ORDER BY date DESC
");
$stmt->execute([$user_id]);
$bmi_history = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>BMI Calculator</h1>
    <p>Check your BMI and stay healthy</p>
</div>

<div class="card">
    <h2>BMI Calculator</h2>
    
    <?php if ($latest_bmi): ?>
        <div class="bmi-result">
            <h3>Your Latest BMI</h3>
            <div class="bmi-value"><?php echo round($latest_bmi['bmi'], 1); ?></div>
            <div class="bmi-category <?php echo strtolower(str_replace(' ', '_', $latest_bmi['category'])); ?>">
                <?php echo $latest_bmi['category']; ?>
            </div>
            <p>Recorded on: <?php echo $latest_bmi['date']; ?></p>
        </div>
    <?php endif; ?>
    
    <h3>Calculate BMI</h3>
    <form method="POST">
        <div class="form-group">
            <label>Weight (kg)</label>
            <input type="number" name="weight" step="0.1" min="1" required>
        </div>
        
        <div class="form-group">
            <label>Height (cm)</label>
            <input type="number" name="height" step="0.1" min="1" required>
        </div>
        
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <button type="submit">Calculate BMI</button>
    </form>
</div>

<div class="card">
    <h3>BMI History</h3>
    
    <?php if (count($bmi_history) > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Weight (kg)</th>
                <th>Height (cm)</th>
                <th>BMI</th>
                <th>Category</th>
            </tr>
            <?php foreach ($bmi_history as $record): ?>
            <tr>
                <td><?php echo $record['date']; ?></td>
                <td><?php echo $record['weight']; ?></td>
                <td><?php echo $record['height']; ?></td>
                <td><?php echo round($record['bmi'], 1); ?></td>
                <td>
                    <span class="bmi-category <?php echo strtolower(str_replace(' ', '_', $record['category'])); ?>">
                        <?php echo $record['category']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No BMI records yet.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>BMI Categories</h3>
    <div class="bmi-categories">
        <div class="category-item underweight">
            <h4>Underweight</h4>
            <p>BMI below 18.5</p>
        </div>
        <div class="category-item normal">
            <h4>Normal weight</h4>
            <p>BMI 18.5 - 24.9</p>
        </div>
        <div class="category-item overweight">
            <h4>Overweight</h4>
            <p>BMI 25 - 29.9</p>
        </div>
        <div class="category-item obese">
            <h4>Obese</h4>
            <p>BMI 30 and above</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>