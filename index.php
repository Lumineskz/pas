<?php
// index.php - Main Adoption Wall
require_once 'config.php';
requireLogin();

// Get all available pets
$query = "SELECT p.*, u.username, u.full_name 
          FROM pets p 
          JOIN users u ON p.user_id = u.user_id 
          WHERE p.status = 'available' 
          ORDER BY p.created_at DESC";
$pets = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Wall - Pet Adoption Center</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🐾 Pet Adoption Center</h1>
            <div class="nav-links">
                <a href="index.php" class="active">Browse Pets</a>
                <a href="post_pet.php">Post Pet</a>
                <a href="my_pets.php">My Pets</a>
                <span class="user-info">👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Available Pets for Adoption</h2>
            <a href="post_pet.php" class="btn btn-primary">+ Post a Pet</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if ($pets->num_rows === 0): ?>
            <div class="empty-state">
                <p>No pets available for adoption at the moment.</p>
                <a href="post_pet.php" class="btn btn-primary">Be the first to post!</a>
            </div>
        <?php else: ?>
            <div class="pets-grid">
                <?php while ($pet = $pets->fetch_assoc()): ?>
                    <div class="pet-card">
                        <div class="pet-image">
                            <?php if ($pet['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="<?php echo htmlspecialchars($pet['pet_name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-image">🐾</div>
                            <?php endif; ?>
                        </div>
                        <div class="pet-info">
                            <h3><?php echo htmlspecialchars($pet['pet_name']); ?></h3>
                            <div class="pet-details">
                                <span class="detail-item">
                                    <strong>Species:</strong> <?php echo htmlspecialchars($pet['species']); ?>
                                </span>
                                <span class="detail-item">
                                    <strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?>
                                </span>
                                <span class="detail-item">
                                    <strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years
                                </span>
                                <span class="detail-item">
                                    <strong>Gender:</strong> <?php echo htmlspecialchars($pet['gender']); ?>
                                </span>
                            </div>
                            <p class="pet-description"><?php echo htmlspecialchars($pet['description']); ?></p>
                            <p class="posted-by">Posted by: <?php echo htmlspecialchars($pet['full_name']); ?></p>
                            
                            <?php if ($pet['user_id'] != $_SESSION['user_id']): ?>
                                <a href="adoption_form.php?pet_id=<?php echo $pet['pet_id']; ?>" class="btn btn-adopt">🏠 Adopt Me</a>
                            <?php else: ?>
                                <button class="btn btn-disabled" disabled>Your Pet</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
