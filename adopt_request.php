<?php
// adopt_request.php - Submit Adoption Request
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pet_id'])) {
    $pet_id = intval($_POST['pet_id']);
    $requester_id = $_SESSION['user_id'];
    
    // Get pet details and owner
    $stmt = $conn->prepare("SELECT user_id, pet_name FROM pets WHERE pet_id = ? AND status = 'available'");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $pet = $result->fetch_assoc();
        $owner_id = $pet['user_id'];
        $pet_name = $pet['pet_name'];
        
        // Check if user is not the owner
        if ($owner_id != $requester_id) {
            // Check if request already exists
            $stmt = $conn->prepare("SELECT request_id FROM adoption_requests WHERE pet_id = ? AND requester_id = ? AND status = 'pending'");
            $stmt->bind_param("ii", $pet_id, $requester_id);
            $stmt->execute();
            $existing = $stmt->get_result();
            
            if ($existing->num_rows === 0) {
                // Create adoption request
                $message = "I would like to adopt " . $pet_name;
                $stmt = $conn->prepare("INSERT INTO adoption_requests (pet_id, requester_id, owner_id, message) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiis", $pet_id, $requester_id, $owner_id, $message);
                
                if ($stmt->execute()) {
                    $request_id = $conn->insert_id;
                    
                    // Create notification for owner
                    $notification_message = $_SESSION['full_name'] . " wants to adopt your pet: " . $pet_name;
                    $stmt = $conn->prepare("INSERT INTO notifications (user_id, request_id, message) VALUES (?, ?, ?)");
                    $stmt->bind_param("iis", $owner_id, $request_id, $notification_message);
                    $stmt->execute();
                    
                    $_SESSION['success'] = "Adoption request sent successfully!";
                } else {
                    $_SESSION['error'] = "Failed to send adoption request.";
                }
            } else {
                $_SESSION['error'] = "You have already requested to adopt this pet.";
            }
        } else {
            $_SESSION['error'] = "You cannot adopt your own pet.";
        }
    } else {
        $_SESSION['error'] = "Pet not found or no longer available.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: index.php");
exit();
?>
