<?php
require_once '../configuration/config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['UserID'])) {
    $userID = intval($_GET['UserID']);
    
    // Query to get user details
    $user_sql = "SELECT Username, FullName, Grade FROM users WHERE UserID = $userID AND RoleID = 1";
    $user_result = $conn->query($user_sql);

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        echo "<h4>Voting History for " . htmlspecialchars($user['FullName']) . " (" . htmlspecialchars($user['Username']) . ")</h4>";
        echo "<p>Grade: " . htmlspecialchars($user['Grade']) . "</p>";

        // Query to get the user's voting history
        $history_sql = "SELECT positions.position_name, candidates.full_name AS candidate_name, user_votes.voted_at
                        FROM user_votes
                        JOIN candidates ON user_votes.candidate_id = candidates.id
                        JOIN positions ON user_votes.position_id = positions.id
                        WHERE user_votes.user_id = $userID
                        ORDER BY user_votes.voted_at";

        $history_result = $conn->query($history_sql);

        if ($history_result->num_rows > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Position</th><th>Candidate</th><th>Voted At</th></tr></thead>";
            echo "<tbody>";

            // Output data of each row
            while($row = $history_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["position_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["candidate_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["voted_at"]) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "No voting history found for this student.";
        }
    } else {
        echo "User not found or not a student.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
