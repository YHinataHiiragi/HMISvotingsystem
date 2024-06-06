<?php
require_once '../configuration/config.php'; // Assuming you have already established a database connection

// Check if the user is logged in
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: index.php");
    exit();
}

// Fetch user's information from the database
$userID = $_SESSION['UserID'];
$query = "SELECT * FROM users WHERE UserID = $userID";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Determine the grade range for candidates based on the user's grade
if ($user['Grade'] >= 1 && $user['Grade'] <= 6) {
    $gradeCondition = "WHERE c.grade BETWEEN 1 AND 6";
} else {
    $gradeCondition = "WHERE c.grade BETWEEN 7 AND 12";
}

// Fetch candidates based on the user's grade and group them by position
$query = "SELECT p.position_name, c.id, c.full_name, c.picture
          FROM positions p
          LEFT JOIN candidates c ON p.id = c.position_id
          $gradeCondition
          ORDER BY p.id";
$result = mysqli_query($conn, $query);

// Check if the query executed successfully
if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../icons/hmis.ico">
    <link rel="stylesheet" href="style.css">
    <style>
.card {
    display: flex;
    justify-content: center; /* Horizontally center the content */
    align-items: center; /* Vertically center the content */
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    width: 150px;
    height: 150px;
    text-align: center;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 0 auto; /* Center horizontally */
}

.card-link {
    display: block; /* Make the link fill the entire width of its container */
    text-decoration: none;
    color: #333;
    font-weight: bold;
    font-size: 16px;
}

.card-link:hover .card {
    background-color: #e0e0e0;
}

</style>
</head>
<body>
<div class="header">
    <div class="header-content">
        
        <img src="../images/hmis.png" alt="HMIS LOGO" class="logo">
        <div class="headername">
            <h1>Student Government Council</h1>
            <h3>Digital Voting System</h3>
        </div>
        <div class="headname">
            <h4>Welcome, <?php echo $user['FullName']; ?>!</h4>
            <h5>Grade: <?php echo $user['Grade']; ?></h5>
        </div>
        <a href="../logout.php"><button class="logout-btn">Logout</button></a>
    </div>
</div>
    <div class="container">
    <div style="text-align: center;">
    <img src="../images/hmis.png" style="width: 150px; height: 150px;">
</div>


        <h1 style="text-align: center;">Student Dashboard</h1>
        
        <a href="vote.php" class="card-link">
            <div class="card">
                VOTE NOW DIGITALLY 
            </div>
        </a>

        <ul>
            

        </ul>
    </div>
    <footer style="text-align: center; padding: 20px;">
    If there is a problem, please report it at the <a href="help_desk_student.php" style="text-decoration: none; color: blue;">Help Desk</a>.<br><br>
    <span style="margin-bottom: 1px; text-align: center; font-size: 10px">&copy; 2024 Harvesters' Missions International School. All rights reserved.</span><br>
    <span style="margin-top: 1px; text-align: center; font-size: 10px">Developed by Hanz Eduard Maclan, IT</span>
    </footer>

</body>
</html>
