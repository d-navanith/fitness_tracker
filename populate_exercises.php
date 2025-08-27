<?php
require_once 'config/db.php';

$exercises = [
    [
        'name' => 'Push-ups',
        'description' => 'A classic bodyweight exercise that targets the chest, shoulders, and triceps.',
        'instructions' => "1. Start in a plank position with hands placed slightly wider than shoulder-width apart.\n2. Lower your body until your chest nearly touches the floor.\n3. Push back up to the starting position.",
        'muscle_group' => 'chest',
        'equipment' => 'none',
        'difficulty' => 'beginner',
        'image_url' => 'images/exercises/pushups.jpg'
    ],
    [
        'name' => 'Squats',
        'description' => 'A fundamental lower body exercise that targets the quadriceps, hamstrings, and glutes.',
        'instructions' => "1. Stand with feet shoulder-width apart.\n2. Lower your body by bending your knees and hips.\n3. Go as low as you can comfortably, then push back up.",
        'muscle_group' => 'legs',
        'equipment' => 'none',
        'difficulty' => 'beginner',
        'image_url' => 'images/exercises/squats.jpg'
    ],
    [
        'name' => 'Bench Press',
        'description' => 'A compound exercise that primarily targets the chest muscles.',
        'instructions' => "1. Lie on a bench with your feet flat on the floor.\n2. Grip the barbell with hands slightly wider than shoulder-width.\n3. Lower the bar to your chest, then press it back up.",
        'muscle_group' => 'chest',
        'equipment' => 'barbell',
        'difficulty' => 'intermediate',
        'image_url' => 'images/exercises/bench_press.jpg'
    ],
    [
        'name' => 'Deadlift',
        'description' => 'A compound exercise that works multiple muscle groups including the back, legs, and core.',
        'instructions' => "1. Stand with feet hip-width apart, toes under the bar.\n2. Bend at the hips and knees to grab the bar.\n3. Stand up by extending your hips and knees, keeping the bar close to your body.",
        'muscle_group' => 'back',
        'equipment' => 'barbell',
        'difficulty' => 'advanced',
        'image_url' => 'images/exercises/deadlift.jpg'
    ],
    [
        'name' => 'Pull-ups',
        'description' => 'An upper body exercise that targets the back and biceps.',
        'instructions' => "1. Hang from a bar with hands slightly wider than shoulder-width.\n2. Pull your body up until your chin clears the bar.\n3. Lower yourself back down with control.",
        'muscle_group' => 'back',
        'equipment' => 'none',
        'difficulty' => 'intermediate',
        'image_url' => 'images/exercises/pullups.jpg'
    ],
    [
        'name' => 'Plank',
        'description' => 'An isometric core exercise that strengthens the abdominal muscles.',
        'instructions' => "1. Start in a push-up position but with your weight on your forearms.\n2. Keep your body in a straight line from head to heels.\n3. Hold this position for the desired time.",
        'muscle_group' => 'core',
        'equipment' => 'none',
        'difficulty' => 'beginner',
        'image_url' => 'images/exercises/plank.jpg'
    ],
    [
        'name' => 'Lunges',
        'description' => 'A lower body exercise that targets the quadriceps and glutes.',
        'instructions' => "1. Stand with feet hip-width apart.\n2. Step forward with one leg and lower your hips until both knees are bent at 90 degrees.\n3. Push back to the starting position and repeat with the other leg.",
        'muscle_group' => 'legs',
        'equipment' => 'none',
        'difficulty' => 'beginner',
        'image_url' => 'images/exercises/lunges.jpg'
    ],
    [
        'name' => 'Shoulder Press',
        'description' => 'An exercise that targets the shoulder muscles.',
        'instructions' => "1. Stand with feet shoulder-width apart, holding dumbbells at shoulder height.\n2. Press the dumbbells overhead until your arms are fully extended.\n3. Lower them back to shoulder height with control.",
        'muscle_group' => 'shoulders',
        'equipment' => 'dumbbells',
        'difficulty' => 'intermediate',
        'image_url' => 'images/exercises/shoulder_press.jpg'
    ],
    [
        'name' => 'Bicep Curls',
        'description' => 'An isolation exercise that targets the biceps.',
        'instructions' => "1. Stand with feet shoulder-width apart, holding dumbbells at your sides.\n2. Curl the weights up by bending your elbows.\n3. Lower them back down with control.",
        'muscle_group' => 'arms',
        'equipment' => 'dumbbells',
        'difficulty' => 'beginner',
        'image_url' => 'images/exercises/bicep_curls.jpg'
    ],
    [
        'name' => 'Running',
        'description' => 'A cardiovascular exercise that improves endurance and burns calories.',
        'instructions' => "1. Start at a comfortable pace.\n2. Maintain good posture with your head up and shoulders relaxed.\n3. Swing your arms naturally and land on your midfoot.",
        'muscle_group' => 'cardio',
        'equipment' => 'none',
        'difficulty' => 'beginner',
        'image_url' => 'images/exercises/running.jpg'
    ]
];

foreach ($exercises as $exercise) {
    $stmt = $pdo->prepare("
        INSERT INTO exercises (name, description, instructions, muscle_group, equipment, difficulty, image_url)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $exercise['name'],
        $exercise['description'],
        $exercise['instructions'],
        $exercise['muscle_group'],
        $exercise['equipment'],
        $exercise['difficulty'],
        $exercise['image_url']
    ]);
}

echo "Exercises populated successfully!";
?>