<?php
// delete_pet.php
require_once 'config.php';

// 1. Security Check: Is the user logged in?
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: my_pets.php");
    exit();
}

$pet_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

// 2. Authorization Check: Only delete if the pet belongs to this user
// Using BOTH pet_id and user_id in the WHERE clause prevents URL hacking
$stmt = $conn->prepare("DELETE FROM pets WHERE pet_id = ? AND user_id = ?");
$stmt->bind_param("ii", $pet_id, $user_id);

if ($stmt->execute()) {
    // Redirect with a success flag
    header("Location: my_pets.php?deleted=1");
} else {
    // Redirect with an error flag
    header("Location: my_pets.php?error=1");
}
exit();