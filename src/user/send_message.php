<?php
// Establish database connection
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}
require_once '../configuration/config.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message']; // Retrieve message from form
    $user_id = $_SESSION['UserID']; // Replace with the actual user ID
    $adminId = null; // Assuming the user is sending the message, so no admin ID

    // Insert message into database
    $stmt = $conn->prepare("INSERT INTO messages (user_id, admin_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userId, $adminId, $message);
    
    if ($stmt->execute()) {
        echo "Message sent successfully.";
    } else {
        echo "Error sending message: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
