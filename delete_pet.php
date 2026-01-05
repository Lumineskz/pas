<?php
// delete_pet.php
require_once 'config.php';

if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $pet_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Verify ownership before deleting
    $stmt = $conn->prepare("DELETE FROM pets WHERE pet_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $pet_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: my_pets.php?success=1");
    } else {
        header("Location: my_pets.php?error=1");
    }
} else {
    header("Location: my_pets.php");
}
exit();