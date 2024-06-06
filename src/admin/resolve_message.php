<?php
require_once '../configuration/config.php';

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Update the status of the messages to resolved
    $sql = "UPDATE help_desk_messages SET status = 'resolved' WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Insert a resolution message with admin_id set to 0
    $resolutionMessage = "𝙏𝙝𝙞𝙨 𝙝𝙚𝙡𝙥 𝙧𝙚𝙦𝙪𝙚𝙨𝙩 𝙝𝙖𝙨 𝙗𝙚𝙚𝙣 𝙧𝙚𝙨𝙤𝙡𝙫𝙚𝙙.";
    $admin_id = 0;
    $sql = "INSERT INTO help_desk_messages (user_id, admin_id, message, status) VALUES (?, ?, ?, 'resolved')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $admin_id, $resolutionMessage);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    echo "Success";
} else {
    echo "No user ID provided";
}
?>
