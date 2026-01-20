<?php
// edit_pet.php - Edit Pet Information
require_once 'config.php';
requireLogin();

$error = '';
$success = '';
$pet = null;

// Create uploads directory if it doesn't exist
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Get pet_id from URL
if (!isset($_GET['id'])) {
    header("Location: my_pets.php");
    exit();
}

$pet_id = intval($_GET['id']);

// Get pet details
$stmt = $conn->prepare("SELECT * FROM pets WHERE pet_id = ? AND user_id = ?");
$stmt->bind_param("ii", $pet_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['error'] = "Pet not found.";
    header("Location: my_pets.php");
    exit();
}

$pet = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pet_name = trim($_POST['pet_name']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $image_url = $pet['image_url']; // Keep existing image by default
    
    // Validation
    if (empty($pet_name) || empty($species) || empty($breed) || empty($gender) || empty($description)) {
        $error = "Please fill in all required fields!";
    } elseif ($age < 0 || $age > 30) {
        $error = "Please enter a valid age!";
    } else {
        // Handle image upload if new image provided
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
                // Delete old image if exists
                if (!empty($pet['image_url']) && file_exists($upload_dir . basename($pet['image_url']))) {
                    unlink($upload_dir . basename($pet['image_url']));
                }
                
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
        
        // Only proceed if no image error
        if (!$error) {
            $stmt = $conn->prepare("UPDATE pets SET pet_name = ?, species = ?, breed = ?, age = ?, gender = ?, description = ?, image_url = ? WHERE pet_id = ? AND user_id = ?");
            $stmt->bind_param("sssisssii", $pet_name, $species, $breed, $age, $gender, $description, $image_url, $pet_id, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Pet information updated successfully!";
                header("Location: my_pets.php");
                exit();
            } else {
                $error = "Failed to update pet. Please try again.";
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
    <title>Edit Pet - Pet Adoption Center</title>
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
                <span class="user-info">👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2>Edit Pet Information</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="pet-form" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Pet Name *</label>
                        <input type="text" name="pet_name" value="<?php echo htmlspecialchars($pet['pet_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Species (e.g., Dog, Cat, Rabbit) *</label>
                        <input type="text" name="species" value="<?php echo htmlspecialchars($pet['species']); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Breed *</label>
                        <input type="text" name="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Age (in years) *</label>
                        <input type="number" name="age" min="0" max="30" value="<?php echo $pet['age']; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Gender *</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Male" <?php echo $pet['gender'] == 'Male' ? 'checked' : ''; ?> required>
                            Male
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="Female" <?php echo $pet['gender'] == 'Female' ? 'checked' : ''; ?> required>
                            Female
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Pet Image (Optional)</label>
                    <?php if (!empty($pet['image_url'])): ?>
                        <div style="margin-bottom: 1rem;">
                            <p style="color: #718096; font-size: 0.9rem;">Current image:</p>
                            <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="Current pet image" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="pet_image" accept="image/*">
                    <small>Accepted formats: JPG, PNG, GIF (Max 5MB). Leave empty to keep current image.</small>
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="5" required placeholder="Tell us about your pet's personality, habits, and why they need a new home..."><?php echo htmlspecialchars($pet['description']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="my_pets.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
