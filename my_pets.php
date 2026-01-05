<?php
// my_pets.php - View User's Posted Pets
require_once 'config.php';
requireLogin();

// Get user's pets
$stmt = $conn->prepare("SELECT * FROM pets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pets = $stmt->get_result();

$notification_count = getNotificationCount($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pets - Pet Adoption Center</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🐾 Pet Adoption Center</h1>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <a href="post_pet.php">Post Pet</a>
                <a href="my_pets.php" class="active">My Pets</a>
                <a href="notifications.php" class="notification-link">
                    🔔 Notifications
                    <?php if ($notification_count > 0): ?>
                        <span class="badge"><?php echo $notification_count; ?></span>
                    <?php endif; ?>
                </a>
                <span class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <header class="page-header">
            <h2>My Posted Pets</h2>
            <p>Manage the pets you have listed for adoption.</p>
        </header>

        <div class="rooms"> <?php if ($pets->num_rows > 0): ?>
                <?php while ($pet = $pets->fetch_assoc()): ?>
                    <div class="card-room"> <h4>
                            <a href="pet_details.php?id=<?= $pet['pet_id'] ?>">
                                <?= htmlspecialchars($pet['name']) ?>
                            </a>
                        </h4>
                        
                        <p><strong>Type:</strong> <?= htmlspecialchars($pet['species']) ?></p>
                        <p class="pet-desc"><?= htmlspecialchars($pet['description']) ?></p>
                        
                        <div class="card-actions" style="margin-top: 15px; display: flex; gap: 10px;">
                            <a href="edit_pet.php?id=<?= $pet['pet_id'] ?>" class="link" style="font-size: 14px;">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <a href="delete_pet.php?id=<?= $pet['pet_id'] ?>" 
                               class="link" 
                               style="color: #ff6b6b; font-size: 14px;"
                               onclick="return confirm('Are you sure you want to remove this listing?')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; width: 100%; color: white; margin-top: 50px;">
                    <p>You haven't posted any pets for adoption yet.</p>
                    <a href="post_pet.php" class="create-room" style="display: inline-block; margin-top: 20px;">Post your first pet</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php" class="link">← Back to All Pets</a>
        </div>
    </div>
</body>
</html>