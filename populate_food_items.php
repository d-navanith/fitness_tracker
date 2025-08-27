<?php
require_once 'config/db.php';

$stmt = $pdo->query("SHOW TABLES LIKE 'food_items'");
if ($stmt->rowCount() == 0) {

    $pdo->exec("CREATE TABLE food_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        calories_per_serving INT NOT NULL,
        serving_size VARCHAR(50) NOT NULL,
        protein DECIMAL(5,2),
        carbs DECIMAL(5,2),
        fat DECIMAL(5,2)
    )");
}

$food_items = [
    [
        'name' => 'Apple',
        'calories_per_serving' => 95,
        'serving_size' => '1 medium (182g)',
        'protein' => 0.5,
        'carbs' => 25,
        'fat' => 0.3
    ],
    [
        'name' => 'Banana',
        'calories_per_serving' => 105,
        'serving_size' => '1 medium (118g)',
        'protein' => 1.3,
        'carbs' => 27,
        'fat' => 0.4
    ],
    [
        'name' => 'Chicken Breast',
        'calories_per_serving' => 165,
        'serving_size' => '100g',
        'protein' => 31,
        'carbs' => 0,
        'fat' => 3.6
    ],
    [
        'name' => 'Brown Rice',
        'calories_per_serving' => 216,
        'serving_size' => '1 cup (195g)',
        'protein' => 5,
        'carbs' => 45,
        'fat' => 1.8
    ],
    [
        'name' => 'Egg',
        'calories_per_serving' => 70,
        'serving_size' => '1 large (50g)',
        'protein' => 6,
        'carbs' => 0.6,
        'fat' => 5
    ],
    [
        'name' => 'Greek Yogurt',
        'calories_per_serving' => 100,
        'serving_size' => '1 cup (245g)',
        'protein' => 17,
        'carbs' => 6,
        'fat' => 0.7
    ],
    [
        'name' => 'Broccoli',
        'calories_per_serving' => 25,
        'serving_size' => '1 cup (91g)',
        'protein' => 3,
        'carbs' => 5,
        'fat' => 0.3
    ],
    [
        'name' => 'Salmon',
        'calories_per_serving' => 206,
        'serving_size' => '100g',
        'protein' => 22,
        'carbs' => 0,
        'fat' => 13
    ],
    [
        'name' => 'Sweet Potato',
        'calories_per_serving' => 103,
        'serving_size' => '1 medium (128g)',
        'protein' => 2.3,
        'carbs' => 24,
        'fat' => 0.1
    ],
    [
        'name' => 'Almonds',
        'calories_per_serving' => 160,
        'serving_size' => '1 oz (28g)',
        'protein' => 6,
        'carbs' => 6,
        'fat' => 14
    ]
];

foreach ($food_items as $item) {
    $stmt = $pdo->prepare("
        INSERT INTO food_items (name, calories_per_serving, serving_size, protein, carbs, fat)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $item['name'],
        $item['calories_per_serving'],
        $item['serving_size'],
        $item['protein'],
        $item['carbs'],
        $item['fat']
    ]);
}

echo "Food items populated successfully!";
?>