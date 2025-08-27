<?php
require_once 'config/session.php';
require_once 'config/db.php';
$user_id = $_SESSION['user_id'];

// Get workout data for charts
$stmt = $pdo->prepare("
    SELECT date, SUM(duration) as total_duration, SUM(calories) as total_calories, COUNT(*) as workout_count 
    FROM workouts 
    WHERE user_id = ? 
    GROUP BY date 
    ORDER BY date ASC 
    LIMIT 30
");
$stmt->execute([$user_id]);
$workout_data = $stmt->fetchAll();

$labels = [];
$duration_values = [];
$calories_values = [];
$workout_counts = [];
foreach ($workout_data as $data) {
    $labels[] = $data['date'];
    $duration_values[] = $data['total_duration'];
    $calories_values[] = $data['total_calories'];
    $workout_counts[] = $data['workout_count'];
}

// Get exercise distribution
$stmt = $pdo->prepare("
    SELECT exercise_name, COUNT(*) as count 
    FROM workouts 
    WHERE user_id = ? 
    GROUP BY exercise_name 
    ORDER BY count DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$exercise_distribution = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Progress Tracking</h1>
    <p>Monitor your fitness improvements over time</p>
</div>

<div class="card">
    <h2>Progress Tracking</h2>

    <h3>Workout Duration (minutes)</h3>
    <div style="width: 100%; height: 300px;">
        <canvas id="durationChart"></canvas>
    </div>

    <h3>Calories Burned</h3>
    <div style="width: 100%; height: 300px;">
        <canvas id="caloriesChart"></canvas>
    </div>

    <h3>Workout Frequency</h3>
    <div style="width: 100%; height: 300px;">
        <canvas id="frequencyChart"></canvas>
    </div>

    <h3>Exercise Distribution</h3>
    <div style="width: 100%; height: 300px;">
        <canvas id="exerciseChart"></canvas>
    </div>
</div>

<div class="card">
    <h3>Recent Activity</h3>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM workouts WHERE user_id = ? ORDER BY date DESC LIMIT 10");
    $stmt->execute([$user_id]);
    $recent = $stmt->fetchAll();
    ?>
    
    <?php if (count($recent) > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Exercise</th>
                <th>Duration</th>
                <th>Calories</th>
            </tr>
            <?php foreach ($recent as $workout): ?>
            <tr>
                <td><?php echo $workout['date']; ?></td>
                <td><?php echo $workout['exercise_name']; ?></td>
                <td><?php echo $workout['duration']; ?> min</td>
                <td><?php echo $workout['calories'] ?: 'N/A'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No workout data available</p>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = {
        labels: <?php echo json_encode($labels); ?>,
        duration: <?php echo json_encode($duration_values); ?>,
        calories: <?php echo json_encode($calories_values); ?>,
        frequency: <?php echo json_encode($workout_counts); ?>,
        exercises: <?php echo json_encode($exercise_distribution); ?>
    };

    const durationCtx = document.getElementById('durationChart').getContext('2d');
    new Chart(durationCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Workout Duration (min)',
                data: chartData.duration,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const caloriesCtx = document.getElementById('caloriesChart').getContext('2d');
    new Chart(caloriesCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Calories Burned',
                data: chartData.calories,
                backgroundColor: 'rgba(231, 76, 60, 0.7)',
                borderColor: '#e74c3c',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const frequencyCtx = document.getElementById('frequencyChart').getContext('2d');
    new Chart(frequencyCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Workouts per Day',
                data: chartData.frequency,
                backgroundColor: 'rgba(46, 204, 113, 0.7)',
                borderColor: '#2ecc71',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    const exerciseCtx = document.getElementById('exerciseChart').getContext('2d');
    new Chart(exerciseCtx, {
        type: 'doughnut',
        data: {
            labels: chartData.exercises.map(item => item.exercise_name),
            datasets: [{
                data: chartData.exercises.map(item => item.count),
                backgroundColor: [
                    '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>