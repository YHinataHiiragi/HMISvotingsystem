<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}

// Assuming you have established a database connection
require_once '../configuration/config.php';

// Retrieve the user's ID from the session
$userID = $_SESSION['UserID'];

// Fetch user's information from the database
$query = "SELECT * FROM users WHERE UserID = $userID";
$result = mysqli_query($conn, $query);

// Check if the query executed successfully
if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

// Fetch user's information from the fetched row
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Confirmation</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="../icons/hmis.ico">
    <!-- Add your CSS stylesheets or link to a CSS file here -->
</head>
<body>
    <div class="container">
        <h2>Vote Confirmation</h2>
        <p>Thank you, <?php echo $user['FullName']; ?>, for submitting your vote!</p>
        <!-- You can add additional content or instructions here -->
        <p><a href="dashboard.php">Return to Dashboard</a></p>
    </div>
</body>
</html>
