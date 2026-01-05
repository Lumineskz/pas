<?php
// my_pets.php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch pets belonging to the user
$stmt = $conn->prepare("SELECT * FROM pets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pets = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pets - Pet Adoption Center</title>
    <link rel="stylesheet" href="style.css"> <script src="https://kit.fontawesome.com/5f3c0ac785.js" crossorigin="anonymous"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🐾 Pet Center</h1>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <a href="post_pet.php">Post Pet</a>
                <a href="my_pets.php" class="active">My Pets</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>My Posted Pets</h2>
            <a href="post_pet.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Pet</a>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Action completed successfully!</div>
        <?php endif; ?>

        <div class="pets-grid">
            <?php if ($pets->num_rows > 0): ?>
                <?php while ($pet = $pets->fetch_assoc()): ?>
                    <div class="pet-card">
                        <div class="pet-image">
                            <div class="placeholder-image">🐾</div>
                        </div>
                        <div class="pet-info">
                            <h3><?= htmlspecialchars($pet['name'] ?? $pet['pet_name'] ?? 'Unnamed Pet') ?></h3>
                            <p class="pet-description"><?= htmlspecialchars($pet['description']) ?></p>
                            
                            <div class="pet-details">
                                <div class="detail-item"><strong>Species:</strong> <?= htmlspecialchars($pet['species']) ?></div>
                            </div>

                            <div class="form-actions" style="margin-top: 20px;">
                                <a href="edit_pet.php?id=<?= $pet['pet_id'] ?>" class="btn btn-secondary" style="flex: 1;">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </a>
                                <a href="delete_pet.php?id=<?= $pet['pet_id'] ?>" 
                                   class="btn btn-danger" 
                                   style="flex: 1;"
                                   onclick="return confirm('Remove this listing permanently?')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>You haven't listed any pets for adoption yet.</p>
                    <a href="post_pet.php" class="btn btn-primary">Post a Pet</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>