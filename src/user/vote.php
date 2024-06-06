<?php
// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have established a database connection
    require_once '../configuration/config.php';

    // Check if the user is logged in
    if (!isset($_SESSION['UserID'])) {
        header("Location: index.php");
        exit();
    }

    // Retrieve the user's ID from the session
    $userID = $_SESSION['UserID'];

    // Process the submitted vote
    foreach ($_POST as $candidateID) {
        // Sanitize input to prevent SQL injection (assuming you're using MySQLi)
        $candidateID = intval($candidateID); // Ensure it's an integer

        // Retrieve the position ID associated with the candidate ID
        $query = "SELECT position_id FROM candidates WHERE id = $candidateID";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "Error: " . mysqli_error($conn);
            // Handle the error, log it, or display a user-friendly message
            continue; // Skip to the next candidate
        }

        $row = mysqli_fetch_assoc($result);
        $positionID = $row['position_id'];

        // Check if the user has already voted for this position
        $checkQuery = "SELECT * FROM user_votes WHERE user_id = $userID AND position_id = $positionID";
        $checkResult = mysqli_query($conn, $checkQuery);

        // If the user has already voted for this position, skip to the next candidate
        if (mysqli_num_rows($checkResult) > 0) {
            continue;
        }

        // Insert the vote into the user_votes table
        $insertQuery = "INSERT INTO user_votes (user_id, position_id, candidate_id) VALUES ($userID, $positionID, $candidateID)";
        $insertResult = mysqli_query($conn, $insertQuery);

        // Check for errors
        if (!$insertResult) {
            echo "Error: " . mysqli_error($conn);
            // Handle the error, log it, or display a user-friendly message
            continue; // Skip to the next candidate
        }

        // Fetch the old vote count
        $oldVoteCountQuery = "SELECT vote_count FROM vote_counts WHERE candidate_id = $candidateID";
        $oldVoteCountResult = mysqli_query($conn, $oldVoteCountQuery);
        $oldVoteCount = 0;
        if ($oldVoteCountResult && mysqli_num_rows($oldVoteCountResult) > 0) {
            $oldVoteCount = mysqli_fetch_assoc($oldVoteCountResult)['vote_count'];
        }

        // Optionally, update the vote count for the selected candidate in the vote_counts table
        $updateQuery = "INSERT INTO vote_counts (candidate_id, vote_count) VALUES ($candidateID, 1)
                        ON DUPLICATE KEY UPDATE vote_count = vote_count + 1";
        $updateResult = mysqli_query($conn, $updateQuery);

        // Handle errors if necessary
        if (!$updateResult) {
            echo "Error: " . mysqli_error($conn);
            // Handle the error, log it, or display a user-friendly message
        } else {
            // Fetch the new vote count
            $newVoteCountQuery = "SELECT vote_count FROM vote_counts WHERE candidate_id = $candidateID";
            $newVoteCountResult = mysqli_query($conn, $newVoteCountQuery);
            $newVoteCount = mysqli_fetch_assoc($newVoteCountResult)['vote_count'];

            // Log the change
            $logChangeQuery = "INSERT INTO change_log (user_id, candidate_id, position_id, old_vote_count, new_vote_count) VALUES ($userID, $candidateID, $positionID, $oldVoteCount, $newVoteCount)";
            mysqli_query($conn, $logChangeQuery);
        }
    }

    // Redirect the user to a confirmation page or the voting page
    header("Location: confirmation.php");
    exit();
} else {
    // If the form is not submitted, redirect back to the voting page
    header("Location: index.php");
    exit();
}
?>
