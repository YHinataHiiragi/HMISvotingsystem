<?php
//get_messages.php
require_once '../configuration/config.php';

// Check if the request method is POST and user_id is set
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
} elseif (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
} else {
    echo "<p>Invalid request.</p>";
    exit;
}

// Function to fetch messages for a specific user
function fetchMessages($user_id, $conn) {
    $sql = "SELECT * FROM help_desk_messages WHERE user_id = ? ORDER BY timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        // Fetch sender's full name for user and admin
        $userFullName = getFullName($row['user_id'], $conn);
        $adminFullName = getFullName($row['admin_id'], $conn);
        // Add sender's full name to message data
        $row['user_name'] = $userFullName;
        $row['admin_name'] = $adminFullName;
        $messages[] = $row; // Fetching all message details
    }
    return $messages;
}

// Fetch messages for the user
$messages = fetchMessages($user_id, $conn);

// Check if the admin is submitting a new message
if (isset($_POST['message'])) {
    // Get the message content submitted by the admin
    $admin_message = $_POST['message'];

    // Fetch admin ID based on role ID from users table
    $admin_id = getAdminId($conn);

    // Insert the message into the database with admin_id and user_id
    $sql = "INSERT INTO help_desk_messages (user_id, admin_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $admin_id, $admin_message);
    $stmt->execute();

    // Check if the message was inserted successfully
    if ($stmt->affected_rows > 0) {
        // Redirect to get_messages.php to reload the content
        header("Location: get_messages.php?user_id=$user_id");
        exit(); // Exit to prevent further execution
    }
}

// Check if the session is being closed by admin
if (isset($_POST['close_session']) && $_POST['close_session'] == true) {
    // Notify the user that admin closed the chat session
    echo "<div class='admin-message'>Admin has closed the chat session.</div>";
    exit;
}

// Output messages
if (!empty($messages)) {
    foreach ($messages as $message) {
        // Output messages after other potential header-related actions
        $messageContent = htmlspecialchars($message['message']);
        $userName = htmlspecialchars($message['user_name']);
        $adminName = htmlspecialchars($message['admin_name']);
        // Determine sender's name
        $senderName = ($message['admin_id'] !== null) ? $adminName : $userName;
        // Chat message style based on sender type
        $messageClass = ($message['admin_id'] !== null) ? 'admin-message' : 'user-message';
        // Format timestamp
        $timestamp = date('Y-m-d H:i:s', strtotime($message['timestamp']));
        echo "<div class='$messageClass'><strong>$senderName:</strong> $messageContent <span class='timestamp' style='font-size: 0.8em; color: #999;text-align: left; display: block;'>sent: $timestamp</span></div>";
    }
} else {
    echo "<p>No messages found.</p>";
}


// Function to get full name based on user ID
function getFullName($userId, $conn) {
    $sql = "SELECT FullName FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ($row) ? $row['FullName'] : 'System';
}

// Function to fetch admin ID based on role ID
function getAdminId($conn) {
    $role_id = 2; // Assuming role ID for admin is 2
    $sql = "SELECT UserID FROM users WHERE RoleID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ($row) ? $row['UserID'] : null;
}
?>
