<?php
// post_pet.php - Post Pet for Adoption
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

// Create uploads directory if it doesn't exist
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pet_name = trim($_POST['pet_name']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $image_url = '';
    
    if (empty($pet_name) || empty($species) || empty($breed) || empty($gender) || empty($description)) {
        $error = "Please fill in all required fields!";
    } elseif ($age < 0 || $age > 30) {
        $error = "Please enter a valid age!";
    } else {
        // Handle image upload
        if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['pet_image']['tmp_name'];
            $file_name = $_FILES['pet_image']['name'];
            $file_size = $_FILES['pet_image']['size'];
            
            // Validate file
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file_ext, $allowed_ext)) {
                $error = "Only JPG, PNG, and GIF files are allowed!";
            } elseif ($file_size > $max_size) {
                $error = "File size must be less than 5MB!";
            } else {
                // Generate unique filename
                $new_filename = 'pet_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    $image_url = 'uploads/' . $new_filename;
                } else {
                    $error = "Failed to upload image. Please try again.";
                }
            }
        }
        
        // Only proceed if no image error or no image required
        if (!$error) {
            $stmt = $conn->prepare("INSERT INTO pets (user_id, pet_name, species, breed, age, gender, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("isssisss", $_SESSION['user_id'], $pet_name, $species, $breed, $age, $gender, $description, $image_url);
                
                if ($stmt->execute()) {
                    $success = "Pet posted successfully!";
                    $_POST = array();
                } else {
                    $error = "Failed to post pet: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
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
            <h1 class="logo">Pet Adoption Center</h1>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <a href="post_pet.php" class="active">Post Pet</a>
                <a href="my_pets.php">My Pets</a>
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
            
            <form method="POST" action="" class="pet-form" enctype="multipart/form-data">
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
                    <label>Pet Image (Optional)</label>
                    <input type="file" name="pet_image" accept="image/*">
                    <small>Accepted formats: JPG, PNG, GIF (Max 5MB)</small>
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
