<?php
require_once '../configuration/config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['log_id']) && isset($_POST['new_vote_count'])) {
    $logID = $_POST['log_id'];
    $newVoteCount = $_POST['new_vote_count'];

    // Fetch old log details
    $sql = "SELECT * FROM change_log WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $logID);
    $stmt->execute();
    $result = $stmt->get_result();
    $log = $result->fetch_assoc();

    // Log the change
    $logChangeQuery = "INSERT INTO change_log (user_id, candidate_id, position_id, old_vote_count, new_vote_count) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($logChangeQuery);
    $stmt->bind_param("iiiii", $log['user_id'], $log['candidate_id'], $log['position_id'], $log['new_vote_count'], $newVoteCount);
    $stmt->execute();

    // Update the log
    $updateLogQuery = "UPDATE change_log SET new_vote_count = ? WHERE log_id = ?";
    $stmt = $conn->prepare($updateLogQuery);
    $stmt->bind_param("ii", $newVoteCount, $logID);
    $stmt->execute();

    echo "Log updated successfully.";
}

$conn->close();
?>
