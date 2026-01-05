<?php
// post_pet.php - Post Pet for Adoption
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pet_name = trim($_POST['pet_name']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    
    if (empty($pet_name) || empty($species) || empty($breed) || empty($gender) || empty($description)) {
        $error = "Please fill in all required fields!";
    } elseif ($age < 0 || $age > 30) {
        $error = "Please enter a valid age!";
    } else {
        $stmt = $conn->prepare("INSERT INTO pets (user_id, pet_name, species, breed, age, gender, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssisss", $_SESSION['user_id'], $pet_name, $species, $breed, $age, $gender, $description, $image_url);
        
        if ($stmt->execute()) {
            $success = "Pet posted successfully!";
            $_POST = array();
        } else {
            $error = "Failed to post pet. Please try again.";
        }
    }
}

$notification_count = getNotificationCount($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Pet - Pet Adoption Center</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">🐾 Pet Adoption Center</h1>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <a href="post_pet.php" class="active">Post Pet</a>
                <a href="my_pets.php">My Pets</a>
                <a href="notifications.php" class="notification-link">
                    🔔 Notifications
                    <?php if ($notification_count > 0): ?>
                        <span class="badge"><?php echo $notification_count; ?></span>
                    <?php endif; ?>
                </a>
                <span class="user-info">👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Post a Pet for Adoption</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?> <a href="index.php">View all pets</a></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="pet-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Pet Name *</label>
                        <input type="text" name="pet_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Species *</label>
                        <select name="species" required>
                            <option value="">Select Species</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Bird">Bird</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Hamster">Hamster</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Breed *</label>
                        <input type="text" name="breed" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Age (years) *</label>
                        <input type="number" name="age" min="0" max="30" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Gender *</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Male" required> Male
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Female" required> Female
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Image URL (Optional)</label>
                    <input type="url" name="image_url" placeholder="https://example.com/pet-image.jpg">
                    <small>Enter a direct link to an image of your pet</small>
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="5" required placeholder="Tell us about your pet's personality, habits, and why they need a new home..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Post Pet</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
