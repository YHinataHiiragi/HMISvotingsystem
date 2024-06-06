<?php
require_once '../configuration/config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['log_id'])) {
    $logID = $_GET['log_id'];

    $sql = "SELECT * FROM change_log WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $logID);
    $stmt->execute();
    $result = $stmt->get_result();
    $log = $result->fetch_assoc();

    echo json_encode($log);
}

$conn->close();
?>
