<?php
// Establish database connection
require_once '../configuration/config.php';

// Fetch messages with sender's full name and timestamp
$sql = "SELECT m.*, 
               CASE 
                   WHEN m.admin_id IS NOT NULL THEN ua.FullName
                   ELSE uu.FullName
               END AS senderFullName,
               m.timestamp AS messageTimestamp
        FROM help_desk_messages m
        LEFT JOIN users uu ON m.user_id = uu.UserID
        LEFT JOIN users ua ON m.admin_id = ua.UserID
        ORDER BY m.timestamp ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Extract timestamp and format it
        $timestamp = strtotime($row['messageTimestamp']);
        $formattedTimestamp = date('Y-m-d H:i:s', $timestamp);

        // Output each message with sender's name and timestamp
        $senderFullName = htmlspecialchars($row['senderFullName']);
        $messageContent = htmlspecialchars($row['message']);
        echo "<p><strong>$senderFullName:</strong> $messageContent <span style='font-size: 0.8em; color: #999;'>sent ($formattedTimestamp)</span></p>";
    }
} else {
    echo "No messages yet.";
}

$conn->close();
?>
