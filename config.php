<?php
// config.php - Database Configuration
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pet_adoption_center');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Email configuration (using PHP mail - configure with your email server)
define('ADMIN_EMAIL', 'noreply@petadoption.local'); // Change this to your email

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Helper function to send email
function sendAdoptionEmail($owner_email, $owner_name, $pet_name, $adopter_name, $adopter_age, $adopter_email, $adopter_phone, $adoption_reason) {
    $subject = "New Adoption Request for " . htmlspecialchars($pet_name);
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
            .content { padding: 20px; background-color: white; }
            .info-block { margin: 15px 0; padding: 15px; background-color: #f0f4ff; border-left: 4px solid #667eea; border-radius: 4px; }
            .info-label { font-weight: bold; color: #667eea; }
            .footer { color: #999; font-size: 12px; text-align: center; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🐾 New Adoption Request!</h2>
            </div>
            <div class='content'>
                <p>Hello " . htmlspecialchars($owner_name) . ",</p>
                <p>Great news! Someone is interested in adopting your pet <strong>" . htmlspecialchars($pet_name) . "</strong>!</p>
                
                <div class='info-block'>
                    <div class='info-label'>Adopter Information:</div>
                    <p><strong>Name:</strong> " . htmlspecialchars($adopter_name) . "</p>
                    <p><strong>Age:</strong> " . htmlspecialchars($adopter_age) . "</p>
                    <p><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($adopter_email) . "'>" . htmlspecialchars($adopter_email) . "</a></p>
                    <p><strong>Phone:</strong> " . htmlspecialchars($adopter_phone) . "</p>
                </div>
                
                <div class='info-block'>
                    <div class='info-label'>Reason for Adoption:</div>
                    <p>" . nl2br(htmlspecialchars($adoption_reason)) . "</p>
                </div>
                
                <p style='margin-top: 20px; color: #666;'>Please contact the adopter directly if you'd like to proceed with the adoption. You can reach them at the email or phone number provided above.</p>
                
                <p>Best regards,<br>🐾 Pet Adoption Center</p>
            </div>
            <div class='footer'>
                <p>This is an automated email from Pet Adoption Center. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . ADMIN_EMAIL . "\r\n";
    
    return mail($owner_email, $subject, $message, $headers);
}
?>