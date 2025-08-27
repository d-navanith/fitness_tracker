<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();
$stmt = $pdo->prepare("SELECT * FROM exercises ORDER BY name");
$stmt->execute();
$exercises = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_plan'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $difficulty = $_POST['difficulty'];
    $duration = (int)$_POST['duration'];
    $days_per_week = (int)$_POST['days_per_week'];
    
    $stmt = $pdo->prepare("
        INSERT INTO workout_plans (user_id, name, description, difficulty, duration, days_per_week)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $name, $description, $difficulty, $duration, $days_per_week]);
    
    $plan_id = $pdo->lastInsertId();

    if (isset($_POST['exercises'])) {
        foreach ($_POST['exercises'] as $exercise) {
            $exercise_id = (int)$exercise['id'];
            $sets = (int)$exercise['sets'];
            $reps = (int)$exercise['reps'];
            $rest_time = (int)$exercise['rest_time'];
            $notes = sanitizeInput($exercise['notes']);
            
            $stmt = $pdo->prepare("
                INSERT INTO workout_plan_exercises (workout_plan_id, exercise_id, sets, reps, rest_time, notes)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$plan_id, $exercise_id, $sets, $reps, $rest_time, $notes]);
        }
    }
    
    header('Location: view_workout_plan.php?id=' . $plan_id);
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Create Workout Plan</h1>
    <p>Design your custom workout routine</p>
</div>

<div class="card">
    <form method="POST" id="workout-plan-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="create_plan" value="1">
        
        <div class="form-section">
            <h2>Plan Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Plan Name</label>
                    <input type="text" name="name" required placeholder="e.g., Upper Body Strength">
                </div>
                
                <div class="form-group">
                    <label>Difficulty</label>
                    <select name="difficulty" required>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration" min="10" required placeholder="e.g., 45">
                </div>
                
                <div class="form-group">
                    <label>Days per Week</label>
                    <input type="number" name="days_per_week" min="1" max="7" required placeholder="e.g., 3">
                </div>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Describe your workout plan..."></textarea>
            </div>
        </div>
        
        <div class="form-section">
            <h2>Exercises</h2>
            
            <div id="exercises-container">
                <div class="exercise-row">
                    <div class="exercise-select-container">
                        <select name="exercises[0][id]" class="exercise-select" required>
                            <option value="">Select an exercise</option>
                            <?php if (count($exercises) > 0): ?>
                                <?php foreach ($exercises as $exercise): ?>
                                    <option value="<?php echo $exercise['id']; ?>"><?php echo htmlspecialchars($exercise['name']); ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No exercises available</option>
                            <?php endif; ?>
                        </select>
                        <button type="button" class="exercise-info-btn" title="Exercise Info">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="exercise-details">
                        <div class="detail-input">
                            <label>Sets</label>
                            <input type="number" name="exercises[0][sets]" min="1" required>
                        </div>
                        
                        <div class="detail-input">
                            <label>Reps</label>
                            <input type="number" name="exercises[0][reps]" min="1" required>
                        </div>
                        
                        <div class="detail-input">
                            <label>Rest (sec)</label>
                            <input type="number" name="exercises[0][rest_time]" min="0" required>
                        </div>
                        
                        <div class="detail-input">
                            <label>Notes</label>
                            <input type="text" name="exercises[0][notes]" placeholder="Optional">
                        </div>
                        
                        <button type="button" class="remove-exercise" title="Remove Exercise">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-exercise" class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Exercise
            </button>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Workout Plan</button>
            <a href="workout_plans.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="exercise-selection-header">
        <h2>Exercise Library</h2>
        <p>Click on an exercise to add it to your workout plan</p>
        
        <div class="exercise-filters">
            <div class="filter-group">
                <label for="muscle-filter">Muscle Group:</label>
                <select id="muscle-filter">
                    <option value="">All</option>
                    <option value="chest">Chest</option>
                    <option value="back">Back</option>
                    <option value="shoulders">Shoulders</option>
                    <option value="arms">Arms</option>
                    <option value="legs">Legs</option>
                    <option value="core">Core</option>
                    <option value="cardio">Cardio</option>
                    <option value="full_body">Full Body</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="equipment-filter">Equipment:</label>
                <select id="equipment-filter">
                    <option value="">All</option>
                    <option value="none">None</option>
                    <option value="dumbbells">Dumbbells</option>
                    <option value="barbell">Barbell</option>
                    <option value="cable">Cable Machine</option>
                    <option value="machine">Machine</option>
                    <option value="kettlebell">Kettlebell</option>
                    <option value="resistance_band">Resistance Band</option>
                    <option value="medicine_ball">Medicine Ball</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="difficulty-filter">Difficulty:</label>
                <select id="difficulty-filter">
                    <option value="">All</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="search-exercises">Search:</label>
                <input type="text" id="search-exercises" placeholder="Search exercises...">
            </div>
        </div>
    </div>
    
    <div class="exercise-library-grid">
        <?php if (count($exercises) > 0): ?>
            <?php foreach ($exercises as $exercise): ?>
                <div class="exercise-library-card" 
                     data-muscle="<?php echo $exercise['muscle_group']; ?>" 
                     data-equipment="<?php echo $exercise['equipment']; ?>" 
                     data-difficulty="<?php echo $exercise['difficulty']; ?>"
                     data-name="<?php echo strtolower($exercise['name']); ?>">
                    
                    <div class="exercise-card-header">
                        <h4><?php echo htmlspecialchars($exercise['name']); ?></h4>
                        <div class="exercise-tags">
                            <span class="tag"><?php echo ucfirst($exercise['muscle_group']); ?></span>
                            <span class="tag"><?php echo ucfirst(str_replace('_', ' ', $exercise['equipment'])); ?></span>
                            <span class="tag"><?php echo ucfirst($exercise['difficulty']); ?></span>
                        </div>
                    </div>
                    
                    <div class="exercise-card-body">
                        <?php if ($exercise['image_url'] && file_exists($exercise['image_url'])): ?>
                            <div class="exercise-image">
                                <img src="<?php echo $exercise['image_url']; ?>" alt="<?php echo htmlspecialchars($exercise['name']); ?>">
                            </div>
                        <?php else: ?>
                            <div class="exercise-image-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <div class="exercise-description">
                            <p><?php echo substr($exercise['description'], 0, 100) . '...'; ?></p>
                        </div>
                    </div>
                    
                    <div class="exercise-card-footer">
                        <button type="button" class="add-exercise-btn" data-exercise-id="<?php echo $exercise['id']; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add to Plan
                        </button>
                        
                        <button type="button" class="view-exercise-btn" data-exercise-id="<?php echo $exercise['id']; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            View Details
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
                <h3>No Exercises Available</h3>
                <p>There are no exercises in the library yet. Please contact your administrator to add exercises.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<div id="exercise-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Exercise Information</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body" id="exercise-info-content">
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let exerciseCount = 1;
    document.getElementById('add-exercise').addEventListener('click', function() {
        addExerciseRow();
    });
    
    function addExerciseRow(exerciseId = null) {
        const container = document.getElementById('exercises-container');
        const newRow = document.createElement('div');
        newRow.className = 'exercise-row';
        
        newRow.innerHTML = `
            <div class="exercise-select-container">
                <select name="exercises[${exerciseCount}][id]" class="exercise-select" required>
                    <option value="">Select an exercise</option>
                    <?php if (count($exercises) > 0): ?>
                        <?php foreach ($exercises as $exercise): ?>
                            <option value="<?php echo $exercise['id']; ?>"><?php echo htmlspecialchars($exercise['name']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No exercises available</option>
                    <?php endif; ?>
                </select>
                <button type="button" class="exercise-info-btn" title="Exercise Info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </button>
            </div>
            
            <div class="exercise-details">
                <div class="detail-input">
                    <label>Sets</label>
                    <input type="number" name="exercises[${exerciseCount}][sets]" min="1" required>
                </div>
                
                <div class="detail-input">
                    <label>Reps</label>
                    <input type="number" name="exercises[${exerciseCount}][reps]" min="1" required>
                </div>
                
                <div class="detail-input">
                    <label>Rest (sec)</label>
                    <input type="number" name="exercises[${exerciseCount}][rest_time]" min="0" required>
                </div>
                
                <div class="detail-input">
                    <label>Notes</label>
                    <input type="text" name="exercises[${exerciseCount}][notes]" placeholder="Optional">
                </div>
                
                <button type="button" class="remove-exercise" title="Remove Exercise">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        `;
        if (exerciseId) {
            newRow.querySelector('.exercise-select').value = exerciseId;
        }
        
        container.appendChild(newRow);
        exerciseCount++;
    }
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-exercise')) {
            e.target.closest('.exercise-row').remove();
        }
    });

    const muscleFilter = document.getElementById('muscle-filter');
    const equipmentFilter = document.getElementById('equipment-filter');
    const difficultyFilter = document.getElementById('difficulty-filter');
    const searchInput = document.getElementById('search-exercises');
    const exerciseCards = document.querySelectorAll('.exercise-library-card');
    
    function filterExercises() {
        const muscleValue = muscleFilter.value.toLowerCase();
        const equipmentValue = equipmentFilter.value.toLowerCase();
        const difficultyValue = difficultyFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();
        
        exerciseCards.forEach(card => {
            const muscle = card.getAttribute('data-muscle').toLowerCase();
            const equipment = card.getAttribute('data-equipment').toLowerCase();
            const difficulty = card.getAttribute('data-difficulty').toLowerCase();
            const name = card.getAttribute('data-name');
            
            const matchesMuscle = !muscleValue || muscle === muscleValue;
            const matchesEquipment = !equipmentValue || equipment === equipmentValue;
            const matchesDifficulty = !difficultyValue || difficulty === difficultyValue;
            const matchesSearch = !searchValue || name.includes(searchValue);
            
            if (matchesMuscle && matchesEquipment && matchesDifficulty && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    muscleFilter.addEventListener('change', filterExercises);
    equipmentFilter.addEventListener('change', filterExercises);
    difficultyFilter.addEventListener('change', filterExercises);
    searchInput.addEventListener('input', filterExercises);

    document.querySelectorAll('.add-exercise-btn').forEach(button => {
        button.addEventListener('click', function() {
            const exerciseId = this.getAttribute('data-exercise-id');
            addExerciseRow(exerciseId);

            document.getElementById('exercises-container').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });

            setTimeout(() => {
                const lastRow = document.querySelector('#exercises-container .exercise-row:last-child');
                if (lastRow) {
                    lastRow.classList.add('highlight-row');
                    setTimeout(() => {
                        lastRow.classList.remove('highlight-row');
                    }, 2000);
                }
            }, 100);
        });
    });

    document.querySelectorAll('.view-exercise-btn').forEach(button => {
        button.addEventListener('click', function() {
            const exerciseId = this.getAttribute('data-exercise-id');
            showExerciseModal(exerciseId);
        });
    });

    const modal = document.getElementById('exercise-modal');
    const modalContent = document.getElementById('exercise-info-content');
    const closeModal = document.querySelector('.close-modal');

    const exerciseData = {
        <?php foreach ($exercises as $exercise): ?>
        '<?php echo $exercise['id']; ?>': {
            name: '<?php echo addslashes($exercise['name']); ?>',
            description: '<?php echo addslashes($exercise['description']); ?>',
            instructions: '<?php echo addslashes($exercise['instructions']); ?>',
            muscleGroup: '<?php echo $exercise['muscle_group']; ?>',
            equipment: '<?php echo $exercise['equipment']; ?>',
            difficulty: '<?php echo $exercise['difficulty']; ?>',
            imageUrl: '<?php echo $exercise['image_url']; ?>'
        },
        <?php endforeach; ?>
    };
    
    function showExerciseModal(exerciseId) {
        if (exerciseData[exerciseId]) {
            const exercise = exerciseData[exerciseId];
            
            modalContent.innerHTML = `
                <div class="exercise-info-modal">
                    <div class="exercise-modal-header">
                        <h4>${exercise.name}</h4>
                        <div class="exercise-tags">
                            <span class="tag">${exercise.muscleGroup}</span>
                            <span class="tag">${exercise.equipment}</span>
                            <span class="tag">${exercise.difficulty}</span>
                        </div>
                    </div>
                    
                    ${exercise.imageUrl && exercise.imageUrl !== 'images/' ? `
                        <div class="exercise-modal-image">
                            <img src="${exercise.imageUrl}" alt="${exercise.name}">
                        </div>
                    ` : ''}
                    
                    <div class="exercise-modal-content">
                        <div class="info-section">
                            <h5>Description</h5>
                            <p>${exercise.description}</p>
                        </div>
                        
                        <div class="info-section">
                            <h5>Instructions</h5>
                            <p>${exercise.instructions.replace(/\n/g, '<br>')}</p>
                        </div>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
        }
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.exercise-info-btn')) {
            const selectElement = e.target.closest('.exercise-row').querySelector('.exercise-select');
            const exerciseId = selectElement.value;
            
            if (exerciseId && exerciseData[exerciseId]) {
                showExerciseModal(exerciseId);
            }
        }
    });
    
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>