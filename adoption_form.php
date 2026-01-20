<?php
// adoption_form.php - Adoption Request Form
require_once 'config.php';
requireLogin();

$error = '';
$success = '';
$pet = null;

// Get pet_id from URL
if (!isset($_GET['pet_id'])) {
    $_SESSION['error'] = "Invalid pet selection.";
    header("Location: index.php");
    exit();
}

$pet_id = intval($_GET['pet_id']);

// Get pet details and owner
$stmt = $conn->prepare("SELECT p.*, u.email, u.full_name as owner_name, u.phone as owner_phone FROM pets p JOIN users u ON p.user_id = u.user_id WHERE p.pet_id = ? AND p.status = 'available'");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['error'] = "Pet not found or no longer available.";
    header("Location: index.php");
    exit();
}

$pet = $result->fetch_assoc();

// Check if user is the owner
if ($pet['user_id'] == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot adopt your own pet.";
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adopter_name = trim($_POST['adopter_name']);
    $adopter_age = intval($_POST['adopter_age']);
    $adopter_email = trim($_POST['adopter_email']);
    $adopter_phone = trim($_POST['adopter_phone']);
    $adoption_reason = trim($_POST['adoption_reason']);
    
    // Validation
    if (empty($adopter_name) || empty($adopter_age) || empty($adopter_email) || empty($adoption_reason)) {
        $error = "All fields are required!";
    } elseif ($adopter_age < 18 || $adopter_age > 120) {
        $error = "Please enter a valid age (18+)!";
    } elseif (!filter_var($adopter_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } else {
        // Check if request already exists
        $stmt = $conn->prepare("SELECT request_id FROM adoption_requests WHERE pet_id = ? AND requester_id = ? AND status = 'pending'");
        $stmt->bind_param("ii", $pet_id, $_SESSION['user_id']);
        $stmt->execute();
        $existing = $stmt->get_result();
        
        if ($existing->num_rows > 0) {
            $error = "You have already requested to adopt this pet.";
        } else {
            // Create adoption request
            $stmt = $conn->prepare("INSERT INTO adoption_requests (pet_id, requester_id, owner_id, adopter_name, adopter_age, adopter_email, adopter_phone, adoption_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiisiss", $pet_id, $_SESSION['user_id'], $pet['user_id'], $adopter_name, $adopter_age, $adopter_email, $adopter_phone, $adoption_reason);
            
            if ($stmt->execute()) {
                // Send email to pet owner
                $email_sent = sendAdoptionEmail(
                    $pet['email'],
                    $pet['owner_name'],
                    $pet['pet_name'],
                    $adopter_name,
                    $adopter_age,
                    $adopter_email,
                    $adopter_phone,
                    $adoption_reason
                );
                
                if ($email_sent) {
                    $_SESSION['success'] = "Adoption request sent successfully! The pet owner will contact you shortly.";
                } else {
                    $_SESSION['success'] = "Adoption request submitted! (Email notification could not be sent - mail server not configured)";
                }
                header("Location: index.php");
                exit();
            } else {
                $error = "Failed to submit adoption request. Please try again.";
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
    <title>Adoption Form - Pet Adoption Center</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo">Pet Adoption Center</h1>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <a href="post_pet.php">Post Pet</a>
                <a href="my_pets.php">My Pets</a>
                <span class="user-info">👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container adoption-form-container">
            <div class="pet-summary">
                <div class="pet-summary-image">
                    <?php if ($pet['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="<?php echo htmlspecialchars($pet['pet_name']); ?>">
                    <?php else: ?>
                        <div class="placeholder-image">🐾</div>
                    <?php endif; ?>
                </div>
                <div class="pet-summary-info">
                    <h2>Interested in <?php echo htmlspecialchars($pet['pet_name']); ?>?</h2>
                    <p class="pet-breed"><?php echo htmlspecialchars($pet['breed']) . " • " . htmlspecialchars($pet['age']) . " years old"; ?></p>
                    <p class="pet-owner">Posted by: <?php echo htmlspecialchars($pet['owner_name']); ?></p>
                </div>
            </div>

            <hr class="form-divider">

            <h3>Please fill out your adoption request</h3>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="pet-form adoption-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="adopter_name" value="<?php echo htmlspecialchars($_POST['adopter_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Age *</label>
                        <input type="number" name="adopter_age" min="18" max="120" value="<?php echo htmlspecialchars($_POST['adopter_age'] ?? ''); ?>" required>
                        <small>Must be 18 or older to adopt</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="adopter_email" value="<?php echo htmlspecialchars($_POST['adopter_email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="adopter_phone" value="<?php echo htmlspecialchars($_POST['adopter_phone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Why do you want to adopt <?php echo htmlspecialchars($pet['pet_name']); ?>? *</label>
                    <textarea name="adoption_reason" rows="6" required placeholder="Tell us about yourself, your living situation, and why you would be a great home for this pet..."></textarea>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Adoption Request</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
