<?php
require_once 'config/session.php';
require_once 'config/db.php';
require_once 'config/security.php';

$user_id = $_SESSION['user_id'];
$csrf_token = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_friend'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $friend_username = sanitizeInput($_POST['friend_username']);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$friend_username, $user_id]);
    $friend = $stmt->fetch();
    
    if ($friend) {
        $stmt = $pdo->prepare("
            SELECT id FROM friends 
            WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)
        ");
        $stmt->execute([$user_id, $friend['id'], $friend['id'], $user_id]);
        $existing = $stmt->fetch();
        
        if (!$existing) {
            $stmt = $pdo->prepare("
                INSERT INTO friends (user_id, friend_id, status)
                VALUES (?, ?, 'pending')
            ");
            $stmt->execute([$user_id, $friend['id']]);
            
            $success = "Friend request sent!";
        } else {
            $error = "You are already friends or have a pending request with this user.";
        }
    } else {
        $error = "User not found.";
    }
}

if (isset($_GET['action']) && isset($_GET['request_id'])) {
    $action = $_GET['action'];
    $request_id = (int)$_GET['request_id'];
    
    if ($action === 'accept' || $action === 'reject') {
        $stmt = $pdo->prepare("
            SELECT * FROM friends 
            WHERE id = ? AND friend_id = ? AND status = 'pending'
        ");
        $stmt->execute([$request_id, $user_id]);
        $request = $stmt->fetch();
        
        if ($request) {
            $status = ($action === 'accept') ? 'accepted' : 'rejected';
            $stmt = $pdo->prepare("UPDATE friends SET status = ? WHERE id = ?");
            $stmt->execute([$status, $request_id]);
            
            header('Location: friends.php');
            exit();
        }
    }
}

$stmt = $pdo->prepare("
    SELECT f.*, u.username 
    FROM friends f 
    JOIN users u ON f.user_id = u.id 
    WHERE f.friend_id = ? AND f.status = 'pending'
");
$stmt->execute([$user_id]);
$friend_requests = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT f.*, u.username 
    FROM friends f 
    JOIN users u ON 
        (f.user_id = u.id AND f.friend_id = ?) OR 
        (f.friend_id = u.id AND f.user_id = ?)
    WHERE f.status = 'accepted'
");
$stmt->execute([$user_id, $user_id]);
$friends = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="page-header">
    <h1>Friends</h1>
    <p>Connect and share your fitness journey</p>
</div>

<div class="card">
    <h2>Friends</h2>
    
    <h3>Add Friend</h3>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="add_friend" value="1">
        
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="friend_username" required>
        </div>
        
        <button type="submit">Send Friend Request</button>
    </form>
</div>

<div class="card">
    <h3>Friend Requests</h3>
    
    <?php if (count($friend_requests) > 0): ?>
        <table>
            <tr>
                <th>Username</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($friend_requests as $request): ?>
            <tr>
                <td><?php echo $request['username']; ?></td>
                <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                <td>
                    <a href="friends.php?action=accept&request_id=<?php echo $request['id']; ?>" class="btn">Accept</a>
                    <a href="friends.php?action=reject&request_id=<?php echo $request['id']; ?>" class="btn">Reject</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No pending friend requests.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Your Friends</h3>
    
    <?php if (count($friends) > 0): ?>
        <div class="friends-grid">
            <?php foreach ($friends as $friend): ?>
                <div class="friend-card">
                    <h4><?php echo $friend['username']; ?></h4>
                    <p>Friends since: <?php echo date('M j, Y', strtotime($friend['created_at'])); ?></p>
                    <a href="profile.php?user=<?php echo $friend['username']; ?>" class="btn">View Profile</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No friends yet. Add some friends to get started!</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>