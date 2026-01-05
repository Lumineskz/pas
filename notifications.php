<?php
// notifications.php - View and Manage Adoption Requests
require_once 'config.php';
requireLogin();

// Handle accept/reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['request_id'])) {
        $request_id = intval($_POST['request_id']);
        $action = $_POST['action'];
        
        // Verify the request belongs to current user
        $stmt = $conn->prepare("SELECT ar.*, p.pet_name, p.pet_id FROM adoption_requests ar JOIN pets p ON ar.pet_id = p.pet_id WHERE ar.request_id = ? AND ar.owner_id = ?");
        $stmt->bind_param("ii", $request_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $request = $result->fetch_assoc();
            
            if ($action === 'accept') {
                // Update request status to accepted
                $stmt = $conn->prepare("UPDATE adoption_requests SET status = 'accepted' WHERE request_id = ?");
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                
                // Update pet status to adopted
                $stmt = $conn->prepare("UPDATE pets SET status = 'adopted' WHERE pet_id = ?");
                $stmt->bind_param("i", $request['pet_id']);
                $stmt->execute();
                
                // Reject all other pending requests for this pet
                $stmt = $conn->prepare("UPDATE adoption_requests SET status = 'rejected' WHERE pet_id = ? AND request_id != ?");
                $stmt->bind_param("ii", $request['pet_id'], $request_id);
                $stmt->execute();
                
                $success = "Adoption request accepted! Pet has been marked as adopted.";
            } elseif ($action === 'reject') {
                // Update request status to rejected
                $stmt = $conn->prepare("UPDATE adoption_requests SET status = 'rejected' WHERE request_id = ?");
                $stmt->bind_param("i", $request_id);
                $stmt->execute();
                
                $success = "Adoption request rejected.";
            }
        }
    }
}

// Mark all notifications as read
$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();

// Get all notifications and requests for current user
$query = "SELECT n.*, ar.status as request_status, ar.pet_id, ar.requester_id,
          u.full_name as requester_name, u.phone as requester_phone,
          p.pet_name
          FROM notifications n
          JOIN adoption_requests ar ON n.request_id = ar.request_id
          JOIN users u ON ar.requester_id = u.user_id
          JOIN pets p ON ar.pet_id = p.pet_id
          WHERE n.user_id = ?
          ORDER BY n.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$notifications = $stmt->get_result();

$notification_count = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Pet Adoption Center</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🐾 Pet Adoption Center</h1>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <a href="post_pet.php">Post Pet</a>
                <a href="my_pets.php">My Pets</a>
                <a href="notifications.php" class="active notification-link">
                    🔔 Notifications
                </a>
                <span class="user-info">👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Adoption Requests & Notifications</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($notifications->num_rows === 0): ?>
            <div class="empty-state">
                <p>No notifications yet.</p>
            </div>
        <?php else: ?>
            <div class="notifications-list">
                <?php while ($notification = $notifications->fetch_assoc()): ?>
                    <div class="notification-card <?php echo $notification['request_status']; ?>">
                        <div class="notification-header">
                            <h3><?php echo htmlspecialchars($notification['message']); ?></h3>
                            <span class="notification-time"><?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?></span>
                        </div>
                        
                        <div class="notification-body">
                            <p><strong>Requester:</strong> <?php echo htmlspecialchars($notification['requester_name']); ?></p>
                            <?php if ($notification['requester_phone']): ?>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($notification['requester_phone']); ?></p>
                            <?php endif; ?>
                            <p><strong>Pet:</strong> <?php echo htmlspecialchars($notification['pet_name']); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="status-badge status-<?php echo $notification['request_status']; ?>">
                                    <?php echo ucfirst($notification['request_status']); ?>
                                </span>
                            </p>
                        </div>
                        
                        <?php if ($notification['request_status'] === 'pending'): ?>
                            <div class="notification-actions">
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $notification['request_id']; ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="btn btn-success">✓ Accept</button>
                                </form>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $notification['request_id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger">✗ Reject</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
